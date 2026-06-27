<?php

namespace Tests\Unit;

use App\Services\Verification\Gemini\GeminiVisionService;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeminiVisionServiceTest extends TestCase
{
    private function tempImage(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'gem').'.png';
        file_put_contents($path, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='));

        return $path;
    }

    public function test_it_skips_when_not_configured(): void
    {
        Config::set('services.gemini.key', null);

        $result = app(GeminiVisionService::class)->crossCheck($this->tempImage(), 'image/png', []);

        $this->assertFalse($result->ok);
        $this->assertSame('not_configured', $result->skippedReason);
    }

    public function test_it_skips_unsupported_media(): void
    {
        Config::set('services.gemini.enabled', true);
        Config::set('services.gemini.key', 'test-key');

        $result = app(GeminiVisionService::class)->crossCheck($this->tempImage(), 'text/plain', []);

        $this->assertFalse($result->ok);
        $this->assertSame('unsupported_media', $result->skippedReason);
    }

    public function test_it_parses_a_successful_gemini_response(): void
    {
        Config::set('services.gemini.enabled', true);
        Config::set('services.gemini.key', 'test-key');
        Config::set('services.gemini.model', 'gemini-1.5-flash');

        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [[
                    'content' => ['parts' => [[
                        'text' => json_encode([
                            'match' => true,
                            'confidence' => 0.93,
                            'issues' => [],
                            'notes' => 'Document matches the official record.',
                        ]),
                    ]]],
                ]],
            ], 200),
        ]);

        $result = app(GeminiVisionService::class)->crossCheck($this->tempImage(), 'image/png', [
            'owner_name' => 'Ahmed Khan',
        ]);

        $this->assertTrue($result->ok);
        $this->assertTrue($result->match);
        $this->assertSame(0.93, $result->confidence);
        $this->assertSame('gemini', $result->provider);
        $this->assertSame([], $result->issues);
    }

    public function test_it_fails_soft_on_api_error(): void
    {
        Config::set('services.gemini.enabled', true);
        Config::set('services.gemini.key', 'test-key');

        Http::fake(['generativelanguage.googleapis.com/*' => Http::response('nope', 500)]);

        $result = app(GeminiVisionService::class)->crossCheck($this->tempImage(), 'image/png', []);

        $this->assertFalse($result->ok);
        $this->assertSame('http_500', $result->skippedReason);
    }
}
