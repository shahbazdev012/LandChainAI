<?php

declare(strict_types=1);

namespace App\Services\Verification;

use App\Services\Ocr\Contracts\OcrEngine;
use App\Services\Ocr\OcrException;
use App\Services\Verification\Gemini\GeminiVisionService;

/**
 * Extracts identifying fields (owner name, CNIC, plot number) from an uploaded
 * document so the public side can find the matching property. Uses Gemini when
 * available (handles handwriting); otherwise falls back to OCR + regex (which
 * reliably recovers a CNIC at minimum).
 */
class DocumentScanner
{
    public function __construct(
        private readonly OcrEngine $ocr,
        private readonly GeminiVisionService $gemini,
    ) {}

    /**
     * @return array{owner_name: string|null, owner_cnic: string|null, property_number: string|null, source: string}
     */
    public function scan(string $absolutePath, string $mimeType): array
    {
        $fields = $this->gemini->extractFields($absolutePath, $mimeType);

        if ($fields !== null && $this->hasAny($fields)) {
            return [...$fields, 'source' => 'ai'];
        }

        return [...$this->ocrFallback($absolutePath), 'source' => 'ocr'];
    }

    /**
     * Like scan(), but also returns a fraud read from the same Gemini call so
     * the public lookup flow can flag tampered documents. When Gemini is
     * unavailable the OCR fallback supplies fields only (fraud read is null).
     *
     * @return array{owner_name: string|null, owner_cnic: string|null, property_number: string|null, source: string, fraud_hint: string|null, confidence: string|null}
     */
    public function scanWithFraud(string $absolutePath, string $mimeType): array
    {
        $fields = $this->gemini->extractWithFraud($absolutePath, $mimeType);

        if ($fields !== null && $this->hasAny($fields)) {
            return [...$fields, 'source' => 'ai'];
        }

        return [...$this->ocrFallback($absolutePath), 'source' => 'ocr', 'fraud_hint' => null, 'confidence' => null];
    }

    /**
     * @param  array{owner_name: string|null, owner_cnic: string|null, property_number: string|null}  $fields
     */
    private function hasAny(array $fields): bool
    {
        return (bool) array_filter([$fields['owner_name'], $fields['owner_cnic'], $fields['property_number']]);
    }

    /**
     * @return array{owner_name: string|null, owner_cnic: string|null, property_number: string|null}
     */
    private function ocrFallback(string $absolutePath): array
    {
        try {
            $text = $this->ocr->extract($absolutePath);
        } catch (OcrException) {
            $text = '';
        }

        return [
            'owner_name' => null,
            'owner_cnic' => $this->matchCnic($text),
            'property_number' => $this->matchPlot($text),
        ];
    }

    private function matchCnic(string $text): ?string
    {
        // 13-digit CNIC, with or without dashes.
        if (preg_match('/\b\d{5}-?\d{7}-?\d\b/', $text, $m)) {
            return $m[0];
        }

        return null;
    }

    private function matchPlot(string $text): ?string
    {
        // Heuristic: a token like "ABC-12-3456" / "DHA-5C-1207".
        if (preg_match('/\b[A-Z]{2,5}\d?-[A-Z0-9]{1,4}-\d{2,5}\b/', strtoupper($text), $m)) {
            return $m[0];
        }

        return null;
    }
}
