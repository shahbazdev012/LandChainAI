<?php

namespace Tests\Unit;

use App\Enums\VerificationStatus;
use App\Models\Property;
use App\Services\Ocr\Contracts\OcrEngine;
use App\Services\Ocr\FakeOcrEngine;
use App\Services\Ocr\OcrException;
use App\Services\Verification\Gemini\GeminiVisionService;
use App\Services\Verification\VerificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerificationServiceTest extends TestCase
{
    use RefreshDatabase;

    private function property(): Property
    {
        return Property::factory()->approved()->create([
            'property_number' => 'LHR-7788-99001',
            'owner_name' => 'Ahmed Khan',
            'owner_cnic' => '35201-1234567-8',
        ]);
    }

    private function service(string $ocrText): VerificationService
    {
        return new VerificationService(
            (new FakeOcrEngine)->always($ocrText),
            app('config'),
            app(GeminiVisionService::class),
        );
    }

    public function test_matching_document_is_verified(): void
    {
        $text = 'Government Title Deed. Property No: LHR-7788-99001. '
            .'Owner: Ahmed Khan. CNIC: 35201-1234567-8. Issued by the registry office.';

        $result = $this->service($text)->verify($this->property(), '/tmp/doc.png', 'image/png');

        $this->assertSame(VerificationStatus::Verified, $result->status);
        $this->assertGreaterThanOrEqual(80, $result->score);
    }

    public function test_unrelated_document_is_rejected(): void
    {
        $text = 'This is a completely unrelated document with no matching registry details whatsoever.';

        $result = $this->service($text)->verify($this->property(), '/tmp/doc.png', 'image/png');

        $this->assertSame(VerificationStatus::Rejected, $result->status);
    }

    public function test_partial_match_is_rejected_without_ai(): void
    {
        // Correct owner and property number, wrong CNIC -> below the verified
        // threshold, and with no AI cross-check the outcome is binary Rejected.
        $text = 'Property No: LHR-7788-99001. Owner: Ahmed Khan. CNIC: 00000-0000000-0.';

        $result = $this->service($text)->verify($this->property(), '/tmp/doc.png', 'image/png');

        $this->assertSame(VerificationStatus::Rejected, $result->status);
    }

    public function test_engine_failure_without_ai_is_rejected(): void
    {
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

        $result = (new VerificationService($failing, app('config'), app(GeminiVisionService::class)))
            ->verify($this->property(), '/tmp/doc.png', 'image/png');

        $this->assertSame(VerificationStatus::Rejected, $result->status);
        $this->assertNotNull($result->notes);
    }
}
