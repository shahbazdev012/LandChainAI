<?php

declare(strict_types=1);

namespace App\Actions\Verification;

use App\Models\Property;
use App\Models\PropertyVerification;
use App\Models\User;
use App\Services\Verification\VerificationService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Runs OCR + AI verification of an ownership document against the approved
 * ground truth and records the attempt (audit trail).
 */
class VerifyOwnershipDocument
{
    private const DISK = 'local';

    public function __construct(private readonly VerificationService $verifier) {}

    /**
     * Verify a freshly uploaded file.
     */
    public function handle(Property $property, UploadedFile $file, ?User $user = null): PropertyVerification
    {
        $path = $file->store("verifications/{$property->id}", self::DISK);
        abort_if($path === false, 500, 'The document could not be stored.');

        return $this->handlePath($property, self::DISK, $path, (string) $file->getMimeType(), $user);
    }

    /**
     * Verify an already-stored file (e.g. an image uploaded once on the search
     * page and reused to verify a chosen property).
     */
    public function handlePath(Property $property, string $disk, string $path, string $mimeType, ?User $user = null): PropertyVerification
    {
        $absolute = Storage::disk($disk)->path($path);

        $result = $this->verifier->verify($property, $absolute, $mimeType);

        return $property->verifications()->create([
            'user_id' => $user?->id,
            'image_disk' => $disk,
            'image_path' => $path,
            'ocr_data' => [
                'score' => $result->score,
                'checks' => $result->checksToArray(),
                'extracted' => $result->extracted,
                'ocr_text' => Str::limit($result->ocrText, 5000),
                'notes' => $result->notes,
            ],
            'ai_result' => $result->ai?->toArray(),
            'final_status' => $result->status,
        ]);
    }
}
