<?php

declare(strict_types=1);

namespace App\Services\Verification\Gemini;

/**
 * Outcome of a Gemini vision cross-check. `ok` is true only when the API was
 * called and returned a parseable verdict; otherwise `skippedReason` explains
 * why (not configured, unsupported media, API error) and the pipeline falls
 * back to OCR-only.
 */
final readonly class GeminiResult
{
    /**
     * @param  list<string>  $issues
     * @param  array{owner_name: string|null, owner_cnic: string|null, property_number: string|null}  $extracted
     * @param  array<string, mixed>|null  $raw
     */
    public function __construct(
        public bool $ok,
        public ?bool $match = null,
        public ?float $confidence = null,
        public array $issues = [],
        public ?string $notes = null,
        public array $extracted = ['owner_name' => null, 'owner_cnic' => null, 'property_number' => null],
        public ?string $provider = null,
        public ?string $model = null,
        public ?string $skippedReason = null,
        public ?array $raw = null,
    ) {}

    public static function skipped(string $reason): self
    {
        return new self(ok: false, skippedReason: $reason);
    }

    /**
     * @return array{provider: string|null, match: bool|null, confidence: float|null, issues: list<string>, notes: string|null}
     */
    public function toArray(): array
    {
        return [
            'provider' => $this->provider,
            'match' => $this->match,
            'confidence' => $this->confidence,
            'issues' => $this->issues,
            'notes' => $this->notes,
        ];
    }
}
