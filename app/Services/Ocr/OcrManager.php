<?php

declare(strict_types=1);

namespace App\Services\Ocr;

use App\Services\Ocr\Contracts\OcrEngine;
use Illuminate\Support\Manager;

/**
 * Resolves the configured OCR engine. Driver is selected via config('ocr.driver').
 *
 * @method OcrEngine driver(string|null $driver = null)
 */
class OcrManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return (string) $this->config->get('ocr.driver', 'fake');
    }

    public function createTesseractDriver(): OcrEngine
    {
        /** @var array{binary?: string, languages?: list<string>} $config */
        $config = $this->config->get('ocr.tesseract', []);

        return new TesseractEngine($config);
    }

    public function createFakeDriver(): OcrEngine
    {
        return new FakeOcrEngine((string) $this->config->get('ocr.fake_text', ''));
    }
}
