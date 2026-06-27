<?php

declare(strict_types=1);

namespace App\Services\Verification;

/**
 * Result of a single verification rule.
 */
final readonly class CheckResult
{
    public function __construct(
        public string $key,
        public string $label,
        public bool $passed,
        public string $message,
        public int $points,
        public int $maxPoints,
    ) {}

    /**
     * @return array{key: string, label: string, passed: bool, message: string, points: int, max_points: int}
     */
    public function toArray(): array
    {
        return [
            'key' => $this->key,
            'label' => $this->label,
            'passed' => $this->passed,
            'message' => $this->message,
            'points' => $this->points,
            'max_points' => $this->maxPoints,
        ];
    }
}
