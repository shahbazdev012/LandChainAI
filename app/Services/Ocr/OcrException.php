<?php

declare(strict_types=1);

namespace App\Services\Ocr;

use RuntimeException;

class OcrException extends RuntimeException
{
    public static function engineUnavailable(string $engine): self
    {
        return new self("The OCR engine [{$engine}] is not available in this environment.");
    }

    public static function unreadable(string $path): self
    {
        return new self("The document [{$path}] could not be read for OCR.");
    }
}
