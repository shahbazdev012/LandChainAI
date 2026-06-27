<?php

namespace Tests\Unit;

use App\Enums\VerificationStatus;
use App\Models\Property;
use App\Models\PropertyDocument;
use App\Services\Ocr\Contracts\OcrEngine;
use App\Services\Ocr\FakeOcrEngine;
use App\Services\Ocr\OcrException;
use App\Services\Verification\VerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerificationServiceTest extends TestCase
{
    use RefreshDatabase;

    private function property(): Property
    {
        return Property::factory()->create([
            'property_number' => 'LHR-7788-99001',
            'owner_name' => 'Ahmed Khan',
            'owner_cnic' => '35201-1234567-8',
        ]);
    }

    private function service(string $ocrText): VerificationService
    {
        return new VerificationService((new FakeOcrEngine)->always($ocrText), app('config'));
    }

    public function test_matching_document_is_verified(): void
    {
        $property = $this->property();
        $document = PropertyDocument::factory()->for($property)->create();

        $text = 'Government Title Deed. Property No: LHR-7788-99001. '
            .'Owner: Ahmed Khan. CNIC: 35201-1234567-8. Issued by the registry office.';

        $result = $this->service($text)->verify($property, $document);

        $this->assertSame(VerificationStatus::Verified, $result->status);
        $this->assertGreaterThanOrEqual(80, $result->score);
    }

    public function test_unrelated_document_is_rejected(): void
    {
        $property = $this->property();
        $document = PropertyDocument::factory()->for($property)->create();

        $text = 'This is a completely unrelated document with no matching registry details whatsoever.';

        $result = $this->service($text)->verify($property, $document);

        $this->assertSame(VerificationStatus::Rejected, $result->status);
        $this->assertLessThan(50, $result->score);
    }

    public function test_partial_match_is_suspicious(): void
    {
        $property = $this->property();
        $document = PropertyDocument::factory()->for($property)->create();

        // Correct owner and property number, but a different CNIC.
        $text = 'Property No: LHR-7788-99001. Owner: Ahmed Khan. CNIC: 00000-0000000-0.';

        $result = $this->service($text)->verify($property, $document);

        $this->assertSame(VerificationStatus::Suspicious, $result->status);
    }

    public function test_engine_failure_is_handled_gracefully(): void
    {
        $property = $this->property();
        $document = PropertyDocument::factory()->for($property)->create();

        $failing = new class implements OcrEngine
        {
            public function extract(string $absolutePath): string
            {
                throw OcrException::engineUnavailable('tesseract');
            }

            public function isAvailable(): bool
            {
                return false;
            }
        };

        $result = (new VerificationService($failing, app('config')))->verify($property, $document);

        $this->assertSame(VerificationStatus::Suspicious, $result->status);
        $this->assertNotNull($result->notes);
    }
}
