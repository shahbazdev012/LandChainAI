<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Public\VerifyOwnershipRequest;
use App\Models\Property;
use App\Services\Verification\DocumentScanner;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Public, unauthenticated document fraud check: upload a property document,
 * read its fields with AI, look the record up in the registry, and report a
 * fraud verdict.
 *
 * Verdict algorithm:
 *  - no registry match  -> "Inconsistency detected" / "High" (no extra AI call)
 *  - match(es) found    -> the fraud read Gemini produced while extracting
 *                          the fields from the same image (single AI call)
 */
class FraudCheckController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('public/FraudCheck', [
            'checked' => false,
            'extracted' => null,
            'fraud' => null,
            'matches' => [],
        ]);
    }

    public function check(VerifyOwnershipRequest $request, DocumentScanner $scanner): Response
    {
        $file = $request->file('document');
        $path = $file->store('fraud-checks', 'local');
        abort_if($path === false, 500, 'The document could not be stored.');

        $extracted = $scanner->scanWithFraud(
            Storage::disk('local')->path($path),
            (string) $file->getMimeType(),
        );

        $filters = [
            'owner_cnic' => $extracted['owner_cnic'],
            'owner_name' => $extracted['owner_name'],
            'property_number' => $extracted['property_number'],
        ];

        $matches = array_filter($filters, static fn ($v): bool => filled($v)) !== []
            ? Property::query()->publicSearch($filters)->latest()->limit(50)->get()
            : collect();

        $fraud = $matches->isEmpty()
            ? ['fraud_hint' => 'Inconsistency detected', 'confidence' => 'High']
            : ['fraud_hint' => $extracted['fraud_hint'], 'confidence' => $extracted['confidence']];

        return Inertia::render('public/FraudCheck', [
            'checked' => true,
            'extracted' => $extracted,
            'fraud' => $fraud,
            'matches' => $matches->map(static fn (Property $p): array => [
                'id' => $p->id,
                'property_number' => $p->property_number,
                'title' => $p->title,
                'owner_name' => $p->owner_name,
                'city' => $p->city,
                'province' => $p->province,
            ])->all(),
        ]);
    }
}
