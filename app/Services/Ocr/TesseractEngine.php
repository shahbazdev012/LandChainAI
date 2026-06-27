<?php

declare(strict_types=1);

namespace App\Services\Ocr;

use App\Services\Ocr\Contracts\OcrEngine;
use thiagoalessio\TesseractOCR\TesseractNotFoundException;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Throwable;

/**
 * Real OCR backed by the Tesseract binary via the thiagoalessio wrapper.
 *
 * Tesseract operates on raster images. PDFs are not handled here; the
 * verification layer steers users towards image scans for AI verification.
 */
class TesseractEngine implements OcrEngine
{
    /**
     * @param  array{binary?: string}  $config
     */
    public function __construct(private readonly array $config = []) {}

    public function extract(string $absolutePath): string
    {
        if (! is_readable($absolutePath)) {
            throw OcrException::unreadable($absolutePath);
        }

        try {
            $ocr = new TesseractOCR($absolutePath);

            if (! empty($this->config['binary'])) {
                $ocr->executable($this->config['binary']);
            }

            // Tesseract defaults to English ("eng"), which suits the documents
            // handled by this registry.
            return trim($ocr->run());
        } catch (TesseractNotFoundException) {
            throw OcrException::engineUnavailable('tesseract');
        } catch (Throwable $e) {
            throw new OcrException($e->getMessage(), previous: $e);
        }
    }

    public function isAvailable(): bool
    {
        $binary = $this->config['binary'] ?? 'tesseract';

        // `command -v` resolves PATH lookups and absolute paths alike.
        exec('command -v '.escapeshellarg($binary).' 2>/dev/null', $output, $exitCode);

        return $exitCode === 0;
    }
}
