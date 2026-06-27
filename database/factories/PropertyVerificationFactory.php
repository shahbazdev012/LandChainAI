<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\VerificationStatus;
use App\Models\Property;
use App\Models\PropertyVerification;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
            'user_id' => null,
            'image_disk' => 'local',
            'image_path' => 'verifications/'.Str::uuid()->toString().'.jpg',
            'ocr_data' => [
                'score' => fake()->numberBetween(40, 100),
                'checks' => [],
                'text_length' => fake()->numberBetween(50, 400),
            ],
            'ai_result' => null,
            'final_status' => $status,
        ];
    }

    public function status(VerificationStatus $status): static
    {
        return $this->state(fn (): array => ['final_status' => $status]);
    }
}
