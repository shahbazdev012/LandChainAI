<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\DocumentType;
use App\Models\Property;
use App\Models\PropertyDocument;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PropertyDocument>
 */
class PropertyDocumentFactory extends Factory
{
    protected $model = PropertyDocument::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->slug(3).'.pdf';

        return [
            'property_id' => Property::factory(),
            'type' => fake()->randomElement(DocumentType::cases()),
            'disk' => 'local',
            'path' => 'properties/documents/'.Str::uuid()->toString().'.pdf',
            'original_name' => $name,
            'mime_type' => 'application/pdf',
            'size' => fake()->numberBetween(20_000, 2_000_000),
            'file_hash' => hash('sha256', Str::random(40)),
            'uploaded_by' => null,
        ];
    }

    public function type(DocumentType $type): static
    {
        return $this->state(fn (): array => ['type' => $type]);
    }
}
