<?php

declare(strict_types=1);

namespace App\Services\Verification;

use App\Enums\VerificationStatus;
use App\Models\Property;
use App\Models\PropertyDocument;
use App\Services\Ocr\Contracts\OcrEngine;
use App\Services\Ocr\OcrException;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * AI-assisted document verification.
 *
 * Extracts text from an uploaded document via OCR, then runs a set of weighted
 * checks comparing the extracted text against the registered property data
 * (property number, owner name, owner CNIC). Produces a confidence score and a
 * Verified / Suspicious / Rejected outcome.
 */
class VerificationService
{
    public function __construct(
        private readonly OcrEngine $ocr,
        private readonly Config $config,
    ) {}

    public function verify(Property $property, PropertyDocument $document): VerificationResult
    {
        try {
            $text = $this->ocr->extract($this->absolutePath($document));
        } catch (OcrException $e) {
            return $this->engineFailure($e);
        }

        $normalizedText = $this->normalize($text);
        $digitStream = $this->digitsOnly($text);

        $checks = [
            $this->checkOcrQuality($text),
            $this->checkPropertyNumber($property, $normalizedText),
            $this->checkOwnerName($property, $normalizedText),
            $this->checkOwnerCnic($property, $digitStream),
        ];

        $score = min(100, array_sum(array_map(static fn (CheckResult $c): int => $c->points, $checks)));
        $status = $this->statusForScore($score);

        return new VerificationResult(
            status: $status,
            score: $score,
            checks: $checks,
            extracted: [
                'text_length' => Str::length($text),
                'property_number_found' => $checks[1]->passed,
                'owner_name_match' => $checks[2]->message,
                'cnic_found' => $checks[3]->passed,
            ],
            ocrText: $text,
        );
    }

    private function checkOcrQuality(string $text): CheckResult
    {
        $weight = $this->weight('ocr_quality');
        $length = Str::length(trim($text));
        $passed = $length >= (int) $this->config->get('verification.min_ocr_length');

        return new CheckResult(
            key: 'ocr_quality',
            label: 'Readable document text',
            passed: $passed,
            message: $passed
                ? "Extracted {$length} characters of text."
                : ($length > 0 ? 'Very little readable text was extracted.' : 'No readable text could be extracted.'),
            points: $passed ? $weight : ($length > 0 ? intdiv($weight, 3) : 0),
            maxPoints: $weight,
        );
    }

    private function checkPropertyNumber(Property $property, string $normalizedText): CheckResult
    {
        $weight = $this->weight('property_number_match');
        $needle = $this->alphanumeric($property->property_number);
        $haystack = $this->alphanumeric($normalizedText);
        $passed = Str::length($needle) >= 3 && str_contains($haystack, $needle);

        return new CheckResult(
            key: 'property_number_match',
            label: 'Property number matches document',
            passed: $passed,
            message: $passed
                ? "Property number {$property->property_number} was found in the document."
                : "Property number {$property->property_number} was not found in the document.",
            points: $passed ? $weight : 0,
            maxPoints: $weight,
        );
    }

    private function checkOwnerName(Property $property, string $normalizedText): CheckResult
    {
        $weight = $this->weight('owner_name_match');

        $tokens = array_values(array_filter(
            explode(' ', $this->normalize($property->owner_name)),
            static fn (string $token): bool => Str::length($token) >= 3,
        ));

        $total = count($tokens);
        $matched = $total === 0 ? 0 : count(array_filter(
            $tokens,
            static fn (string $token): bool => str_contains($normalizedText, $token),
        ));

        $ratio = $total === 0 ? 0.0 : $matched / $total;
        $passed = $ratio >= 0.5;

        return new CheckResult(
            key: 'owner_name_match',
            label: 'Owner name matches document',
            passed: $passed,
            message: "Matched {$matched} of {$total} owner name parts.",
            points: (int) round($weight * $ratio),
            maxPoints: $weight,
        );
    }

    private function checkOwnerCnic(Property $property, string $digitStream): CheckResult
    {
        $weight = $this->weight('owner_cnic_match');
        $cnic = $this->digitsOnly($property->owner_cnic);

        $full = Str::length($cnic) >= 5 && str_contains($digitStream, $cnic);
        $partial = ! $full && Str::length($cnic) >= 7 && str_contains($digitStream, substr($cnic, -7));

        return new CheckResult(
            key: 'owner_cnic_match',
            label: 'Owner CNIC matches document',
            passed: $full,
            message: match (true) {
                $full => 'Owner CNIC was found in the document.',
                $partial => 'A partial CNIC match was found in the document.',
                default => 'Owner CNIC was not found in the document.',
            },
            points: match (true) {
                $full => $weight,
                $partial => intdiv($weight, 2),
                default => 0,
            },
            maxPoints: $weight,
        );
    }

    private function statusForScore(int $score): VerificationStatus
    {
        return match (true) {
            $score >= (int) $this->config->get('verification.thresholds.verified') => VerificationStatus::Verified,
            $score >= (int) $this->config->get('verification.thresholds.suspicious') => VerificationStatus::Suspicious,
            default => VerificationStatus::Rejected,
        };
    }

    private function engineFailure(OcrException $e): VerificationResult
    {
        return new VerificationResult(
            status: VerificationStatus::Suspicious,
            score: 0,
            checks: [new CheckResult(
                key: 'ocr_quality',
                label: 'Readable document text',
                passed: false,
                message: 'The OCR engine could not process this document.',
                points: 0,
                maxPoints: $this->weight('ocr_quality'),
            )],
            extracted: [],
            ocrText: '',
            notes: 'Automated verification could not complete: '.$e->getMessage().' Manual review is recommended.',
        );
    }

    private function absolutePath(PropertyDocument $document): string
    {
        return Storage::disk($document->disk)->path($document->path);
    }

    private function weight(string $key): int
    {
        return (int) $this->config->get("verification.weights.{$key}");
    }

    private function normalize(string $value): string
    {
        $value = Str::lower($value);
        $value = (string) preg_replace('/[^a-z0-9]+/i', ' ', $value);

        return trim((string) preg_replace('/\s+/', ' ', $value));
    }

    private function alphanumeric(string $value): string
    {
        return (string) preg_replace('/[^a-z0-9]/i', '', Str::lower($value));
    }

    private function digitsOnly(string $value): string
    {
        return (string) preg_replace('/\D+/', '', $value);
    }
}
