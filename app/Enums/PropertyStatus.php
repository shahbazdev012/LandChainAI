<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Approval state of the ground-truth property record (admin side).
 *
 * PendingApproval – created by Data Entry, awaiting Officer review.
 * Approved        – approved by an Officer; live ground truth (sealed in chain).
 * Rejected        – rejected by an Officer; Officer/Admin must correct it.
 */
enum PropertyStatus: string
{
    case PendingApproval = 'pending_approval';
    case Approved = 'approved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PendingApproval => 'Pending Approval',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PendingApproval => 'amber',
            self::Approved => 'green',
            self::Rejected => 'red',
        };
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            static fn (self $status): array => ['value' => $status->value, 'label' => $status->label()],
            self::cases(),
        );
    }
}
