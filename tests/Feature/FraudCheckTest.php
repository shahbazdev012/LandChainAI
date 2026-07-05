<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Services\Ocr\Contracts\OcrEngine;
use App\Services\Ocr\FakeOcrEngine;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FraudCheckTest extends TestCase
{
    use RefreshDatabase;

    public function test_fraud_check_page_is_public(): void
    {
        $this->get(route('fraud-check.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('public/FraudCheck')
                ->where('checked', false)
                ->where('fraud', null));
    }

    public function test_no_registry_match_flags_inconsistency_without_gemini_fraud_read(): void
    {
        Storage::fake('local');
        $this->fakeGemini([
            'owner_name' => 'Unknown Person',
            'cnic' => '99999-9999999-9',
            'property_number' => 'XYZ-99-99999',
            'fraud_hint' => 'Document appears genuine',
            'confidence' => 'High',
        ]);

        $this->post(route('fraud-check.check'), ['document' => UploadedFile::fake()->image('doc.png')])
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('public/FraudCheck')
                ->where('checked', true)
                ->where('fraud.fraud_hint', 'Inconsistency detected')
                ->where('fraud.confidence', 'High')
                ->has('matches', 0));
    }

    public function test_registry_match_uses_gemini_fraud_read(): void
    {
        Storage::fake('local');

        Property::factory()->approved()->create([
            'owner_cnic' => '35201-1234567-8',
            'owner_name' => 'Ahmed Khan',
            'property_number' => 'LHR-1234-56789',
        ]);

        $this->fakeGemini([
            'owner_name' => 'Ahmed Khan',
            'cnic' => '35201-1234567-8',
            'property_number' => 'LHR-1234-56789',
            'fraud_hint' => 'Document appears genuine',
            'confidence' => 'High',
        ]);

        $this->post(route('fraud-check.check'), ['document' => UploadedFile::fake()->image('doc.png')])
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('public/FraudCheck')
                ->where('checked', true)
                ->where('fraud.fraud_hint', 'Document appears genuine')
                ->where('fraud.confidence', 'High')
                ->has('matches', 1));
    }

    public function test_registry_match_with_tampering_indicators_is_flagged(): void
    {
        Storage::fake('local');

        Property::factory()->approved()->create([
            'owner_cnic' => '35201-1234567-8',
            'owner_name' => 'Ahmed Khan',
            'property_number' => 'LHR-1234-56789',
        ]);

        $this->fakeGemini([
            'owner_name' => 'Ahmed Khan',
            'cnic' => '35201-1234567-8',
            'property_number' => 'LHR-1234-56789',
            'fraud_hint' => 'Inconsistency detected',
            'confidence' => 'Medium',
        ]);

        $this->post(route('fraud-check.check'), ['document' => UploadedFile::fake()->image('doc.png')])
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('fraud.fraud_hint', 'Inconsistency detected')
                ->where('fraud.confidence', 'Medium')
                ->has('matches', 1));
    }

    public function test_ocr_fallback_match_has_no_image_fraud_read(): void
    {
        Storage::fake('local');

        // Gemini disabled (test env default) -> OCR fallback extracts the CNIC.
        Property::factory()->approved()->create(['owner_cnic' => '35201-1234567-8']);
        $this->app->instance(OcrEngine::class, (new FakeOcrEngine)->always('CNIC 35201-1234567-8'));

        $this->post(route('fraud-check.check'), ['document' => UploadedFile::fake()->image('doc.png')])
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('checked', true)
                ->where('fraud.fraud_hint', null)
                ->where('fraud.confidence', null)
                ->has('matches', 1));
    }

    public function test_document_upload_is_validated(): void
    {
        $this->post(route('fraud-check.check'), [])->assertSessionHasErrors('document');

        $this->post(route('fraud-check.check'), [
            'document' => UploadedFile::fake()->create('doc.txt', 10, 'text/plain'),
        ])->assertSessionHasErrors('document');
    }

    /**
     * @param  array<string, string>  $payload
     */
    private function fakeGemini(array $payload): void
    {
        Config::set('services.gemini.enabled', true);
        Config::set('services.gemini.key', 'test-key');
        Config::set('services.gemini.model', 'gemini-1.5-flash');

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [['text' => json_encode($payload)]]],
                ]],
            ], 200),
        ]);
    }
}
