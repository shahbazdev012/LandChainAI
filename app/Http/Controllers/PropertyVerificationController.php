<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Verification\RunPropertyVerification;
use App\Enums\VerificationStatus;
use App\Exceptions\VerificationException;
use App\Jobs\VerifyPropertyDocument;
use App\Models\Property;
use App\Models\PropertyDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PropertyVerificationController extends Controller
{
    public function store(Request $request, Property $property, RunPropertyVerification $action): RedirectResponse
    {
        $this->authorize('verify', $property);

        $validated = $request->validate([
            'document_id' => ['nullable', 'integer'],
        ]);

        $document = $this->resolveDocument($property, $validated['document_id'] ?? null);

        // Heavy OCR can be pushed off-request; synchronous is the default and
        // gives the officer instant feedback for a single document.
        if (config('verification.async')) {
            VerifyPropertyDocument::dispatch($property->id, $document?->id, $request->user()->id);

            return back()->with('info', 'Verification has been queued and will complete shortly.');
        }

        try {
            $verification = $action->handle($property, $document, $request->user());
        } catch (VerificationException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with(...$this->flashFor($verification->status));
    }

    private function resolveDocument(Property $property, ?int $documentId): ?PropertyDocument
    {
        if ($documentId === null) {
            return null;
        }

        return $property->documents()->findOrFail($documentId);
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function flashFor(VerificationStatus $status): array
    {
        return match ($status) {
            VerificationStatus::Verified => ['success', 'Verification complete — the property was verified.'],
            VerificationStatus::Suspicious => ['warning', 'Verification complete — the document looks suspicious and needs review.'],
            VerificationStatus::Rejected => ['error', 'Verification complete — the document was rejected.'],
        };
    }
}
