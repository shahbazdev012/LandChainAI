<?php

namespace Tests\Feature;

use App\Enums\PropertyStatus;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyManagementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
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

    public function test_guests_cannot_create_properties(): void
    {
        $this->post(route('properties.store'), $this->payload())->assertRedirect(route('login'));
    }

    public function test_data_entry_can_create_a_pending_property(): void
    {
        $clerk = $this->dataEntry();

        $this->actingAs($clerk)
            ->post(route('properties.store'), $this->payload())
            ->assertRedirect();

        $property = Property::firstOrFail();
        $this->assertSame(PropertyStatus::PendingApproval, $property->status);
        $this->assertSame($clerk->id, $property->created_by);
    }

    public function test_data_entry_cannot_update_a_property(): void
    {
        $property = Property::factory()->create();

        $this->actingAs($this->dataEntry())
            ->put(route('properties.update', $property), $this->payload(['title' => 'Hacked']))
            ->assertForbidden();
    }

    public function test_data_entry_cannot_approve_a_property(): void
    {
        $property = Property::factory()->status(PropertyStatus::PendingApproval)->create();

        $this->actingAs($this->dataEntry())
            ->post(route('properties.approve', $property))
            ->assertForbidden();
    }

    public function test_officer_can_update_a_property(): void
    {
        $property = Property::factory()->create();

        $this->actingAs($this->officer())
            ->put(route('properties.update', $property), $this->payload(['title' => 'Updated Title']))
            ->assertRedirect();

        $this->assertSame('Updated Title', $property->fresh()->title);
    }

    public function test_only_admin_can_delete(): void
    {
        $property = Property::factory()->create();

        $this->actingAs($this->officer())
            ->delete(route('properties.destroy', $property))
            ->assertForbidden();

        $this->actingAs($this->admin())
            ->delete(route('properties.destroy', $property))
            ->assertRedirect();

        $this->assertSoftDeleted($property);
    }

    public function test_users_without_a_staff_role_are_forbidden(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('properties.index'))
            ->assertForbidden();
    }
}
