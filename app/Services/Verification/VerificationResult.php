<?php

declare(strict_types=1);

namespace App\Services\Verification;

use App\Enums\VerificationStatus;

final readonly class VerificationResult
{
    /**
     * @param  list<CheckResult>  $checks
     * @param  array<string, mixed>  $extracted
     */
    public function __construct(
        public VerificationStatus $status,
        public int $score,
        public array $checks,
        public array $extracted,
        public string $ocrText,
        public ?string $notes = null,
    ) {}

    /**
     * @return list<array{key: string, label: string, passed: bool, message: string, points: int, max_points: int}>
     */
    public function checksToArray(): array
    {
        return array_map(static fn (CheckResult $check): array => $check->toArray(), $this->checks);
    }
}
