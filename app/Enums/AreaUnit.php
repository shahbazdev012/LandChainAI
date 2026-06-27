<?php

declare(strict_types=1);

namespace App\Enums;

enum AreaUnit: string
{
    case SquareFeet = 'sqft';
    case SquareYards = 'sqyd';
    case Marla = 'marla';
    case Kanal = 'kanal';
    case Acre = 'acre';

    public function label(): string
    {
        return match ($this) {
            self::SquareFeet => 'Square Feet',
            self::SquareYards => 'Square Yards',
            self::Marla => 'Marla',
            self::Kanal => 'Kanal',
            self::Acre => 'Acre',
        };
    }

    /**
     * @return array<int, array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            static fn (self $unit): array => ['value' => $unit->value, 'label' => $unit->label()],
            self::cases(),
        );
    }
}
