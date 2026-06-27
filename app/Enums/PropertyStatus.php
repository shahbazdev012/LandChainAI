<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Lifecycle state of a registered property.
 *
 * Draft            – created but not yet submitted into the registry/chain.
 * Pending          – registered and awaiting the automated check.
 * AwaitingApproval – automated check passed; needs a human to approve.
 * Verified         – a reviewer approved the record; trusted.
 * Suspicious       – automated check found inconsistencies (dead end; re-check).
 * Rejected         – automated check failed or the record was rejected.
 */
enum PropertyStatus: string
{
    case Draft = 'draft';
    case Pending = 'pending';
    case AwaitingApproval = 'awaiting_approval';
    case Verified = 'verified';
    case Suspicious = 'suspicious';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Pending => 'Pending Verification',
            self::AwaitingApproval => 'Awaiting Approval',
            self::Verified => 'Verified',
            self::Suspicious => 'Suspicious',
            self::Rejected => 'Rejected',
        };
    }

    /**
     * Semantic colour token consumed by the UI badge component.
     */
    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Pending => 'amber',
            self::AwaitingApproval => 'blue',
            self::Verified => 'green',
            self::Suspicious => 'orange',
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
