<?php

declare(strict_types=1);

namespace App\Services\Ocr;

use App\Services\Ocr\Contracts\OcrEngine;

/**
 * Deterministic OCR engine for tests and environments without a Tesseract
 * binary. Returns pre-seeded text instead of shelling out.
 */
class FakeOcrEngine implements OcrEngine
{
    /** @var list<string> */
    private array $queue = [];

    public function __construct(private string $default = '') {}

    public function always(string $text): self
    {
        $this->default = $text;

        return $this;
    }

    /**
     * Queue responses to be returned in order, one per extract() call.
     */
    public function queue(string ...$texts): self
    {
        array_push($this->queue, ...$texts);

        return $this;
    }

    public function extract(string $absolutePath): string
    {
        return array_shift($this->queue) ?? $this->default;
    }

    public function isAvailable(): bool
    {
        return true;
    }
}
