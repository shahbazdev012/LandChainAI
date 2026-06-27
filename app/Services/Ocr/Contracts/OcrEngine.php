<?php

declare(strict_types=1);

namespace App\Services\Ocr\Contracts;

use App\Services\Ocr\OcrException;

interface OcrEngine
{
    /**
     * Extract raw text from a document located at the given absolute path.
     *
     * @throws OcrException when extraction cannot be performed
     */
    public function extract(string $absolutePath): string;

    /**
     * Whether this engine is able to run in the current environment.
     */
    public function isAvailable(): bool;
}
