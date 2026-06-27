<?php

namespace Tests\Feature;

use App\Enums\PropertyStatus;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PropertyRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'property_number' => 'LHR-1234-56789',
            'title' => 'Residential Plot 12-A',
            'type' => 'residential',
            'owner_name' => 'Ahmed Khan',
            'owner_cnic' => '35201-1234567-8',
            'address' => '12-A Model Town',
            'city' => 'Lahore',
            'province' => 'Punjab',
            'area_value' => 10,
            'area_unit' => 'marla',
        ], $overrides);
    }

    public function test_guests_cannot_register_properties(): void
    {
        $this->post(route('properties.store'), $this->validPayload())
            ->assertRedirect(route('login'));
    }

    public function test_officer_can_register_a_property_and_it_is_sealed_into_the_chain(): void
    {
        Storage::fake('local');
        $officer = $this->officer();

        $response = $this->actingAs($officer)->post(route('properties.store'), $this->validPayload([
            'documents' => [
                ['type' => 'title_deed', 'file' => UploadedFile::fake()->image('deed.jpg')],
            ],
        ]));

        $property = Property::firstOrFail();

        $response->assertRedirect(route('properties.show', $property));
        $this->assertDatabaseHas('properties', [
            'property_number' => 'LHR-1234-56789',
            'status' => PropertyStatus::Pending->value,
            'registered_by' => $officer->id,
        ]);
        $this->assertDatabaseCount('property_blocks', 1);
        $this->assertDatabaseCount('property_documents', 1);
        Storage::disk('local')->assertExists($property->documents()->first()->path);
    }

    public function test_property_number_is_required(): void
    {
        $this->actingAs($this->officer())
            ->post(route('properties.store'), $this->validPayload(['property_number' => '']))
            ->assertSessionHasErrors('property_number');
    }

    public function test_property_number_must_be_unique(): void
    {
        Property::factory()->create(['property_number' => 'LHR-1234-56789']);

        $this->actingAs($this->officer())
            ->post(route('properties.store'), $this->validPayload())
            ->assertSessionHasErrors('property_number');
    }
}
