<?php

declare(strict_types=1);

namespace App\Enums;

enum PropertyType: string
{
    case Residential = 'residential';
    case Commercial = 'commercial';
    case Agricultural = 'agricultural';
    case Industrial = 'industrial';
    case Plot = 'plot';

    public function label(): string
    {
        return ucfirst($this->value);
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
