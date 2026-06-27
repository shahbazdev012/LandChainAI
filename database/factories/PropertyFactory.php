<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AreaUnit;
use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Models\Property;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Property>
 */
class PropertyFactory extends Factory
{
    protected $model = Property::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $city = fake()->randomElement(['Lahore', 'Karachi', 'Islamabad', 'Faisalabad', 'Multan']);

        return [
            'property_number' => strtoupper(Str::substr($city, 0, 3)).'-'.fake()->unique()->numerify('####-#####'),
            'title' => fake()->randomElement(['Residential Plot', 'Commercial Shop', 'Farm House', 'Office Suite', 'Family Home']).' '.fake()->buildingNumber(),
            'type' => fake()->randomElement(PropertyType::cases()),
            'description' => fake()->optional()->sentence(12),
            'owner_name' => fake()->name(),
            'owner_cnic' => fake()->numerify('#####-#######-#'),
            'owner_contact' => fake()->optional()->phoneNumber(),
            'address' => fake()->streetAddress(),
            'city' => $city,
            'province' => fake()->randomElement(['Punjab', 'Sindh', 'KPK', 'Balochistan', 'ICT']),
            'area_value' => fake()->randomFloat(2, 2, 500),
            'area_unit' => fake()->randomElement(AreaUnit::cases()),
            'status' => PropertyStatus::Pending,
            'registered_by' => User::factory(),
            'verified_at' => null,
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (): array => [
            'status' => PropertyStatus::Verified,
            'verified_at' => now(),
        ]);
    }

    public function status(PropertyStatus $status): static
    {
        return $this->state(fn (): array => ['status' => $status]);
    }
}
