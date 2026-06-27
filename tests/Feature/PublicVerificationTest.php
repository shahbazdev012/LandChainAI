<?php

namespace Tests\Feature;

use App\Enums\PropertyStatus;
use App\Enums\VerificationStatus;
use App\Models\Property;
use App\Services\Ocr\Contracts\OcrEngine;
use App\Services\Ocr\FakeOcrEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PublicVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_page_is_public(): void
    {
        $this->get(route('verify-property.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('public/Search')->where('hasSearched', false));
    }

    public function test_search_returns_only_approved_matches(): void
    {
        Property::factory()->approved()->create(['owner_cnic' => '35201-1234567-8']);
        Property::factory()->status(PropertyStatus::PendingApproval)->create(['owner_cnic' => '35201-1234567-8']);

        $this->get(route('verify-property.index', ['owner_cnic' => '35201-1234567-8']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('public/Search')
                ->where('hasSearched', true)
                ->has('results', 1));
    }

    public function test_detail_is_only_available_for_approved_properties(): void
    {
        $approved = Property::factory()->approved()->create();
        $pending = Property::factory()->status(PropertyStatus::PendingApproval)->create();

        $this->get(route('verify-property.show', $approved))->assertOk();
        $this->get(route('verify-property.show', $pending))->assertNotFound();
    }

    public function test_guest_can_verify_a_document_and_result_is_stored(): void
    {
        Storage::fake('local');

        $property = Property::factory()->approved()->create([
            'property_number' => 'LHR-1234-56789',
            'owner_name' => 'Ahmed Khan',
            'owner_cnic' => '35201-1234567-8',
        ]);

        // Matching OCR text -> verified (Gemini disabled in test env).
        $this->app->instance(OcrEngine::class, (new FakeOcrEngine)->always(
            'Property No: LHR-1234-56789 Owner: Ahmed Khan CNIC: 35201-1234567-8',
        ));

        $this->post(route('verify-property.verify', $property), [
            'document' => UploadedFile::fake()->image('deed.jpg'),
        ])->assertRedirect();

        $this->assertDatabaseCount('property_verifications', 1);
        $verification = $property->verifications()->firstOrFail();
        $this->assertSame(VerificationStatus::Verified, $verification->final_status);
        $this->assertNull($verification->user_id); // guest
    }

    public function test_verification_requires_a_document(): void
    {
        $property = Property::factory()->approved()->create();

        $this->post(route('verify-property.verify', $property), [])
            ->assertSessionHasErrors('document');
    }

    public function test_scan_extracts_fields_and_auto_verifies_a_single_match(): void
    {
        Storage::fake('local');

        $property = Property::factory()->approved()->create([
            'property_number' => 'LHR-1234-56789',
            'owner_name' => 'Ahmed Khan',
            'owner_cnic' => '35201-1234567-8',
        ]);

        // OCR fallback (Gemini disabled in tests) reads CNIC + plot from the text.
        $this->app->instance(OcrEngine::class, (new FakeOcrEngine)->always(
            'Property No: LHR-1234-56789 Owner: Ahmed Khan CNIC: 35201-1234567-8',
        ));

        $this->post(route('verify-property.scan'), [
            'document' => UploadedFile::fake()->image('deed.jpg'),
        ])->assertRedirect(); // single match -> auto-verified -> redirect to result

        $this->assertDatabaseCount('property_verifications', 1);
        $this->assertSame(VerificationStatus::Verified, $property->verifications()->firstOrFail()->final_status);
    }

    public function test_scan_with_no_match_shows_the_search_page(): void
    {
        Storage::fake('local');
        Property::factory()->approved()->create(['owner_cnic' => '35201-1234567-8']);

        // Extracted CNIC matches no approved property.
        $this->app->instance(OcrEngine::class, (new FakeOcrEngine)->always('CNIC: 99999-9999999-9'));

        $this->post(route('verify-property.scan'), [
            'document' => UploadedFile::fake()->image('deed.jpg'),
        ])->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('public/Search')
                ->where('scanned', true)
                ->has('results', 0));
    }
}
