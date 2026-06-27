<?php

declare(strict_types=1);

namespace App\Services\Verification;

use App\Enums\VerificationStatus;
use App\Models\Property;
use App\Services\Ocr\Contracts\OcrEngine;
use App\Services\Ocr\OcrException;
use App\Services\Verification\Gemini\GeminiResult;
use App\Services\Verification\Gemini\GeminiVisionService;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\Str;

/**
 * Verifies a public user's uploaded ownership document against the approved
 * ground-truth property record:
 *   1. rule-based OCR comparison (property number, owner name, owner CNIC)
 *   2. optional Gemini vision cross-check (authenticity + match)
 * and produces a binary VERIFIED / REJECTED outcome.
 */
class VerificationService
{
    public function __construct(
        private readonly OcrEngine $ocr,
        private readonly Config $config,
        private readonly GeminiVisionService $gemini,
    ) {}

    public function verify(Property $property, string $absolutePath, string $mimeType): VerificationResult
    {
        $ocrAvailable = true;
        $text = '';

        try {
            $text = $this->ocr->extract($absolutePath);
        } catch (OcrException) {
            $ocrAvailable = false;
        }

        $normalizedText = $this->normalize($text);
        $digitStream = $this->digitsOnly($text);

        $checks = [
            $this->checkOcrQuality($text),
            $this->checkPropertyNumber($property, $normalizedText),
            $this->checkOwnerName($property, $normalizedText),
            $this->checkOwnerCnic($property, $digitStream),
        ];

        $ocrScore = min(100, array_sum(array_map(static fn (CheckResult $c): int => $c->points, $checks)));

        $ai = $this->gemini->crossCheck($absolutePath, $mimeType, $this->groundTruth($property));

        $status = $this->decide($ocrScore, $ocrAvailable, $ai);

        $score = $ai->ok && $ai->confidence !== null
            ? (int) round(0.5 * $ocrScore + 0.5 * $ai->confidence * 100)
            : $ocrScore;

        return new VerificationResult(
            status: $status,
            score: $score,
            checks: $checks,
            extracted: [
                'text_length' => Str::length($text),
                'ocr_available' => $ocrAvailable,
                'property_number_found' => $checks[1]->passed,
                'owner_name_match' => $checks[2]->message,
                'cnic_found' => $checks[3]->passed,
            ],
            ocrText: $text,
            notes: $this->note($ocrAvailable, $ai),
            ai: $ai->ok ? $ai : null,
        );
    }

    /**
     * Binary decision. With Gemini: verified only if it confirms a match with
     * high confidence, no flagged issues, and OCR corroborates. Without Gemini:
     * fall back to the OCR score.
     */
    private function decide(int $ocrScore, bool $ocrAvailable, GeminiResult $ai): VerificationStatus
    {
        $suspicious = (int) $this->config->get('verification.thresholds.suspicious');
        $verified = (int) $this->config->get('verification.thresholds.verified');

        if ($ai->ok) {
            $threshold = (float) $this->config->get('services.gemini.confidence_threshold', 0.6);
            $ocrCorroborates = $ocrAvailable ? $ocrScore >= $suspicious : true;

            $pass = $ai->match === true
                && ($ai->confidence ?? 0.0) >= $threshold
                && $ai->issues === []
                && $ocrCorroborates;

            return $pass ? VerificationStatus::Verified : VerificationStatus::Rejected;
        }

        if (! $ocrAvailable) {
            return VerificationStatus::Rejected;
        }

        return $ocrScore >= $verified ? VerificationStatus::Verified : VerificationStatus::Rejected;
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

    /**
     * @return array<string, string|null>
     */
    private function groundTruth(Property $property): array
    {
        return [
            'property_number' => $property->property_number,
            'owner_name' => $property->owner_name,
            'owner_cnic' => $property->owner_cnic,
            'property_type' => $property->type->label(),
            'area' => rtrim(rtrim((string) $property->area_value, '0'), '.').' '.$property->area_unit->label(),
            'address' => $property->address,
            'city' => $property->city,
            'province' => $property->province,
        ];
    }

    private function note(bool $ocrAvailable, GeminiResult $ai): ?string
    {
        if (! $ocrAvailable) {
            return $ai->ok
                ? 'OCR was unavailable; decision based on the AI vision check.'
                : 'Automated verification could not run (OCR unavailable and no AI cross-check). Try again later.';
        }

        if ($ai->ok) {
            return $ai->notes;
        }

        return $ai->skippedReason === 'not_configured'
            ? null
            : 'AI vision cross-check was skipped ('.$ai->skippedReason.'); decision based on OCR only.';
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
