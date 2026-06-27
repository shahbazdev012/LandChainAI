<?php

declare(strict_types=1);

namespace App\Enums;

enum DocumentType: string
{
    case TitleDeed = 'title_deed';
    case SaleAgreement = 'sale_agreement';
    case TaxReceipt = 'tax_receipt';
    case OwnerCnic = 'owner_cnic';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::TitleDeed => 'Title Deed',
            self::SaleAgreement => 'Sale Agreement',
            self::TaxReceipt => 'Tax Receipt',
            self::OwnerCnic => 'Owner CNIC',
            self::Other => 'Other',
        };
    }

    /**
     * Whether this document type is the primary source for AI/OCR verification.
     */
    public function isVerifiable(): bool
    {
        return $this !== self::Other;
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            static fn (self $type): array => ['value' => $type->value, 'label' => $type->label()],
            self::cases(),
        );
    }
}
