<?php

declare(strict_types=1);

namespace App\Services\Verification\Gemini;

use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Gemini multimodal (vision) helper. Two jobs:
 *  - crossCheck(): judge whether a document matches the ground truth & is authentic.
 *  - extractFields(): read owner name / CNIC / plot number off a document image
 *    (including handwriting) so they can drive a search.
 *
 * Designed to fail soft: any misconfiguration or API error is non-fatal.
 */
class GeminiVisionService
{
    private const ENDPOINT = 'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent';

    private const SUPPORTED_MIME = [
        'image/png', 'image/jpeg', 'image/jpg', 'image/webp', 'image/heic', 'image/heif', 'application/pdf',
    ];

    public function __construct(private readonly Config $config) {}

    public function isConfigured(): bool
    {
        return (bool) $this->config->get('services.gemini.enabled', true)
            && filled($this->config->get('services.gemini.key'));
    }

    /**
     * @param  array<string, string|null>  $groundTruth
     */
    public function crossCheck(string $absolutePath, string $mimeType, array $groundTruth): GeminiResult
    {
        if (! $this->isConfigured()) {
            return GeminiResult::skipped('not_configured');
        }

        if (! $this->supports($mimeType)) {
            return GeminiResult::skipped('unsupported_media');
        }

        if (! is_readable($absolutePath)) {
            return GeminiResult::skipped('unreadable_file');
        }

        $response = $this->request($absolutePath, $mimeType, $this->crossCheckPrompt($groundTruth));

        if (! $response['ok']) {
            return GeminiResult::skipped((string) $response['reason']);
        }

        return $this->toResult($response['data']);
    }

    /**
     * Best-effort extraction of identifying fields from a document image.
     *
     * @return array{owner_name: string|null, owner_cnic: string|null, property_number: string|null}|null
     */
    public function extractFields(string $absolutePath, string $mimeType): ?array
    {
        if (! $this->isConfigured() || ! $this->supports($mimeType) || ! is_readable($absolutePath)) {
            return null;
        }

        $response = $this->request($absolutePath, $mimeType, $this->extractPrompt());

        if (! $response['ok'] || $response['data'] === null) {
            return null;
        }

        $data = $response['data'];

        return [
            'owner_name' => $this->cleanString($data['owner_name'] ?? null),
            'owner_cnic' => $this->cleanString($data['cnic'] ?? $data['owner_cnic'] ?? null),
            'property_number' => $this->cleanString($data['property_number'] ?? $data['plot_number'] ?? null),
        ];
    }

    private function supports(string $mimeType): bool
    {
        return in_array(strtolower($mimeType), self::SUPPORTED_MIME, true);
    }

    /**
     * @return array{ok: bool, data: array<string, mixed>|null, reason: string|null}
     */
    private function request(string $absolutePath, string $mimeType, string $prompt): array
    {
        $model = (string) $this->config->get('services.gemini.model');

        try {
            $response = Http::withHeaders(['X-goog-api-key' => (string) $this->config->get('services.gemini.key')])
                ->timeout((int) $this->config->get('services.gemini.timeout', 30))
                ->post(sprintf(self::ENDPOINT, $model), [
                    'contents' => [[
                        'parts' => [
                            ['inline_data' => ['mime_type' => $mimeType, 'data' => base64_encode((string) file_get_contents($absolutePath))]],
                            ['text' => $prompt],
                        ],
                    ]],
                    'generationConfig' => ['temperature' => 0.1, 'response_mime_type' => 'application/json'],
                ]);
        } catch (Throwable $e) {
            Log::warning('Gemini request failed', ['error' => $e->getMessage()]);

            return ['ok' => false, 'data' => null, 'reason' => 'request_failed'];
        }

        if (! $response->successful()) {
            Log::warning('Gemini non-2xx', ['status' => $response->status()]);

            return ['ok' => false, 'data' => null, 'reason' => 'http_'.$response->status()];
        }

        $text = data_get($response->json(), 'candidates.0.content.parts.0.text');

        if (! is_string($text)) {
            return ['ok' => false, 'data' => null, 'reason' => 'empty_response'];
        }

        $decoded = $this->decodeJson($text);

        return $decoded === null
            ? ['ok' => false, 'data' => null, 'reason' => 'unparseable_response']
            : ['ok' => true, 'data' => $decoded, 'reason' => null];
    }

    /**
     * @param  array<string, mixed>|null  $data
     */
    private function toResult(?array $data): GeminiResult
    {
        if ($data === null) {
            return GeminiResult::skipped('unparseable_response');
        }

        $issues = array_values(array_filter(array_map(
            static fn ($i): string => is_string($i) ? $i : (string) json_encode($i),
            (array) ($data['issues'] ?? []),
        )));

        return new GeminiResult(
            ok: true,
            match: filter_var($data['match'] ?? null, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE),
            confidence: isset($data['confidence']) ? max(0.0, min(1.0, (float) $data['confidence'])) : null,
            issues: $issues,
            notes: isset($data['notes']) ? (string) $data['notes'] : null,
            provider: 'gemini',
            model: (string) $this->config->get('services.gemini.model'),
            raw: $data,
        );
    }

    /**
     * @param  array<string, string|null>  $groundTruth
     */
    private function crossCheckPrompt(array $groundTruth): string
    {
        $record = json_encode(array_filter($groundTruth, static fn ($v): bool => $v !== null && $v !== ''), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return <<<PROMPT
        You are a land-registry fraud examiner. The OFFICIAL RECORD (ground truth) is:
        {$record}

        Examine the attached property/ownership document image and decide whether it
        corresponds to the official record and looks authentic.

        Decision rules:
        - Compare ONLY the fields that actually appear in the document against the record.
        - A field ABSENT from the document is NOT a mismatch — ignore it.
        - "match" is FALSE only if (a) a field present in the document CONTRADICTS the record
          (different owner name, CNIC or property number), OR (b) there are clear signs of
          forgery/tampering.
        - Otherwise "match" is TRUE.

        Respond ONLY with strict JSON:
        {"match": true|false, "confidence": 0.0-1.0, "issues": ["..."], "notes": "one sentence"}
        PROMPT;
    }

    private function extractPrompt(): string
    {
        return <<<'PROMPT'
        Read the attached property/ownership document image (it MAY be handwritten).
        Extract these identifying fields if present:
        - owner_name: the property owner's full name
        - cnic: the owner's CNIC / national ID (digits, typically formatted #####-#######-#)
        - property_number: the plot / property / parcel number

        Respond ONLY with strict JSON using null for any field you cannot read:
        {"owner_name": string|null, "cnic": string|null, "property_number": string|null}
        PROMPT;
    }

    private function cleanString(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function decodeJson(string $text): ?array
    {
        $decoded = json_decode($text, true);

        if (is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{.*\}/s', $text, $m)) {
            $decoded = json_decode($m[0], true);

            return is_array($decoded) ? $decoded : null;
        }

        return null;
    }
}
