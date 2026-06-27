<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Outcome of a single AI verification run against a property document.
 */
enum VerificationStatus: string
{
    case Verified = 'verified';
    case Suspicious = 'suspicious';
    case Rejected = 'rejected';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Verified => 'green',
            self::Suspicious => 'orange',
            self::Rejected => 'red',
        };
    }

    /**
     * Map a verification outcome onto the owning property's lifecycle status.
     */
    public function toPropertyStatus(): PropertyStatus
    {
        return match ($this) {
            self::Verified => PropertyStatus::Verified,
            self::Suspicious => PropertyStatus::Suspicious,
            self::Rejected => PropertyStatus::Rejected,
        };
    }
}
