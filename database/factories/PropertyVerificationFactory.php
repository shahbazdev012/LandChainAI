<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\VerificationStatus;
use App\Models\Property;
use App\Models\PropertyVerification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PropertyVerification>
 */
class PropertyVerificationFactory extends Factory
{
    protected $model = PropertyVerification::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(VerificationStatus::cases());

        return [
            'property_id' => Property::factory(),
            'document_id' => null,
            'status' => $status,
            'score' => fake()->numberBetween(40, 100),
            'checks' => [
                ['key' => 'required_fields', 'label' => 'Required fields present', 'passed' => true, 'message' => 'All required fields detected.'],
                ['key' => 'owner_match', 'label' => 'Owner name matches', 'passed' => true, 'message' => 'Owner name found in document.'],
            ],
            'extracted' => ['owner_name' => fake()->name(), 'property_number' => fake()->bothify('???-####-#####')],
            'ocr_text' => fake()->paragraph(),
            'notes' => null,
            'run_by' => null,
        ];
    }

    public function status(VerificationStatus $status): static
    {
        return $this->state(fn (): array => ['status' => $status]);
    }
}
