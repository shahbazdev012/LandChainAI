<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Final outcome of a public user's ownership-document verification attempt.
 */
enum VerificationStatus: string
{
    case Verified = 'verified';
    case Rejected = 'rejected';

    public function label(): string
    {
        return ucfirst($this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Verified => 'green',
            self::Rejected => 'red',
        };
    }
}
