<?php

declare(strict_types=1);

namespace App\Actions\Verification;

use App\Enums\PropertyStatus;
use App\Enums\VerificationStatus;
use App\Exceptions\VerificationException;
use App\Models\Property;
use App\Models\PropertyDocument;
use App\Models\PropertyVerification;
use App\Models\User;
use App\Services\Verification\VerificationService;
use Illuminate\Support\Facades\DB;

/**
 * Runs an AI verification pass for a property against one of its documents and
 * records the outcome, updating the property's lifecycle status.
 */
class RunPropertyVerification
{
    public function __construct(private readonly VerificationService $verifier) {}

    public function handle(Property $property, ?PropertyDocument $document = null, ?User $runner = null): PropertyVerification
    {
        $document ??= $this->resolveDocument($property);

        if ($document === null) {
            throw VerificationException::noVerifiableDocument();
        }

        $result = $this->verifier->verify($property, $document);

        return DB::transaction(function () use ($property, $document, $result, $runner): PropertyVerification {
            $verification = $property->verifications()->create([
                'document_id' => $document->id,
                'status' => $result->status,
                'score' => $result->score,
                'checks' => $result->checksToArray(),
                'extracted' => $result->extracted,
                'ocr_text' => $result->ocrText,
                'notes' => $result->notes,
                'run_by' => $runner?->id,
            ]);

            // A passing automated check does NOT verify the property outright —
            // it moves it to "awaiting approval" for a human to sign off. A
            // failing check is terminal (suspicious / rejected).
            $property->update([
                'status' => $result->status === VerificationStatus::Verified
                    ? PropertyStatus::AwaitingApproval
                    : $result->status->toPropertyStatus(),
                'verified_at' => null,
            ]);

            return $verification;
        });
    }

    /**
     * Choose the best document for OCR: prefer image scans of verifiable types.
     */
    private function resolveDocument(Property $property): ?PropertyDocument
    {
        $documents = $property->documents()->latest()->get();

        return $documents->first(fn (PropertyDocument $d): bool => $d->type->isVerifiable() && $d->isImage())
            ?? $documents->first(fn (PropertyDocument $d): bool => $d->type->isVerifiable())
            ?? $documents->first();
    }
}
