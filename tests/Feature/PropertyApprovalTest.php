<?php

namespace Tests\Feature;

use App\Enums\PropertyStatus;
use App\Models\Property;
use App\Models\PropertyBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_officer_approves_and_seals_into_chain(): void
    {
        $property = Property::factory()->status(PropertyStatus::PendingApproval)->create();

        $this->actingAs($officer = $this->officer())
            ->post(route('properties.approve', $property))
            ->assertRedirect()
            ->assertSessionHas('success');

        $fresh = $property->fresh();
        $this->assertSame(PropertyStatus::Approved, $fresh->status);
        $this->assertSame($officer->id, $fresh->approved_by);
        $this->assertNotNull($fresh->approved_at);
        $this->assertDatabaseCount('property_blocks', 1);
        $this->assertSame(1, PropertyBlock::where('property_id', $property->id)->count());
    }

    public function test_officer_can_reject(): void
    {
        $property = Property::factory()->status(PropertyStatus::PendingApproval)->create();

        $this->actingAs($this->officer())
            ->post(route('properties.reject', $property))
            ->assertRedirect();

        $this->assertSame(PropertyStatus::Rejected, $property->fresh()->status);
        $this->assertDatabaseCount('property_blocks', 0);
    }

    public function test_cannot_approve_a_property_that_is_not_pending(): void
    {
        $property = Property::factory()->approved()->create();

        $this->actingAs($this->officer())
            ->post(route('properties.approve', $property))
            ->assertRedirect()
            ->assertSessionHas('error');
    }
}
