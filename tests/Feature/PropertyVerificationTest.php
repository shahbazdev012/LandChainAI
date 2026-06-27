<?php

namespace Tests\Feature;

use App\Enums\PropertyStatus;
use App\Models\Property;
use App\Models\PropertyDocument;
use App\Services\Ocr\Contracts\OcrEngine;
use App\Services\Ocr\FakeOcrEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PropertyVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_passing_check_moves_property_to_awaiting_approval(): void
    {
        $property = Property::factory()->create([
            'property_number' => 'LHR-1234-56789',
            'owner_name' => 'Ahmed Khan',
            'owner_cnic' => '35201-1234567-8',
        ]);
        PropertyDocument::factory()->for($property)->create();

        $this->app->instance(OcrEngine::class, (new FakeOcrEngine)->always(
            'Property No: LHR-1234-56789 Owner: Ahmed Khan CNIC: 35201-1234567-8',
        ));

        $this->actingAs($this->officer())
            ->post(route('properties.verify', $property))
            ->assertRedirect();

        // A passing automated check awaits human approval — it is NOT verified yet.
        $this->assertSame(PropertyStatus::AwaitingApproval, $property->fresh()->status);
        $this->assertNull($property->fresh()->verified_at);
        $this->assertDatabaseCount('property_verifications', 1);
    }

    public function test_failing_check_marks_property_rejected_without_approval_step(): void
    {
        $property = Property::factory()->create([
            'property_number' => 'LHR-1234-56789',
            'owner_name' => 'Ahmed Khan',
            'owner_cnic' => '35201-1234567-8',
        ]);
        PropertyDocument::factory()->for($property)->create();

        $this->app->instance(OcrEngine::class, (new FakeOcrEngine)->always('totally unrelated text'));

        $this->actingAs($this->officer())
            ->post(route('properties.verify', $property))
            ->assertRedirect();

        $this->assertSame(PropertyStatus::Rejected, $property->fresh()->status);
    }

    public function test_verification_without_a_document_fails_gracefully(): void
    {
        $property = Property::factory()->create(['status' => PropertyStatus::Pending]);

        $this->actingAs($this->officer())
            ->post(route('properties.verify', $property))
            ->assertRedirect()
            ->assertSessionHas('error');

        $this->assertSame(PropertyStatus::Pending, $property->fresh()->status);
        $this->assertDatabaseCount('property_verifications', 0);
    }
}
