<?php

namespace Tests\Feature;

use App\Enums\PropertyStatus;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_reviewer_can_approve_a_property_awaiting_approval(): void
    {
        $property = Property::factory()->status(PropertyStatus::AwaitingApproval)->create();

        $this->actingAs($this->officer())
            ->post(route('properties.approve', $property))
            ->assertRedirect()
            ->assertSessionHas('success');

        $fresh = $property->fresh();
        $this->assertSame(PropertyStatus::Verified, $fresh->status);
        $this->assertNotNull($fresh->verified_at);
    }

    public function test_reviewer_can_reject_a_property_awaiting_approval(): void
    {
        $property = Property::factory()->status(PropertyStatus::AwaitingApproval)->create();

        $this->actingAs($this->officer())
            ->post(route('properties.reject', $property))
            ->assertRedirect();

        $this->assertSame(PropertyStatus::Rejected, $property->fresh()->status);
    }

    public function test_cannot_approve_a_property_that_is_not_awaiting_approval(): void
    {
        $property = Property::factory()->status(PropertyStatus::Pending)->create();

        $this->actingAs($this->officer())
            ->post(route('properties.approve', $property))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame(PropertyStatus::Pending, $property->fresh()->status);
    }

    public function test_guests_cannot_approve(): void
    {
        $property = Property::factory()->status(PropertyStatus::AwaitingApproval)->create();

        $this->post(route('properties.approve', $property))->assertRedirect(route('login'));
        $this->assertSame(PropertyStatus::AwaitingApproval, $property->fresh()->status);
    }
}
