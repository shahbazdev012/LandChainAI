<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Verification\VerifyOwnershipDocument;
use App\Enums\PropertyStatus;
use App\Http\Requests\Public\VerifyOwnershipRequest;
use App\Models\Property;
use App\Models\PropertyVerification;
use App\Services\Verification\DocumentScanner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Public, unauthenticated ownership verification: search approved properties,
 * then upload a document to verify ownership against the ground truth.
 */
class PublicVerificationController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->validate([
            'owner_cnic' => ['nullable', 'string', 'max:30'],
            'owner_name' => ['nullable', 'string', 'max:120'],
            'property_number' => ['nullable', 'string', 'max:50'],
            'city' => ['nullable', 'string', 'max:120'],
            'province' => ['nullable', 'string', 'max:120'],
        ]);

        $hasSearched = array_filter($filters, static fn ($v): bool => filled($v)) !== [];

        $results = $hasSearched
            ? Property::query()->publicSearch($filters)->latest()->limit(50)->get()
                ->map(fn (Property $p): array => $this->summary($p))->all()
            : [];

        return Inertia::render('public/Search', [
            'filters' => $filters,
            'hasSearched' => $hasSearched,
            'results' => $results,
            'extracted' => null,
            'scanned' => false,
        ]);
    }

    /**
     * Upload a document on the search page: extract its fields (CNIC, owner,
     * plot number) and find the matching approved property. A single match is
     * verified immediately with the same image; multiple matches are listed for
     * one-click verification (reusing the uploaded image).
     */
    public function scan(VerifyOwnershipRequest $request, DocumentScanner $scanner, VerifyOwnershipDocument $action): Response|RedirectResponse
    {
        $file = $request->file('document');
        $path = $file->store('verifications/scans', 'local');
        abort_if($path === false, 500, 'The document could not be stored.');

        $mime = (string) $file->getMimeType();
        $extracted = $scanner->scan(Storage::disk('local')->path($path), $mime);

        // Keep the uploaded image so a chosen match can be verified without re-uploading.
        $request->session()->put('vp_scan', ['disk' => 'local', 'path' => $path, 'mime' => $mime]);

        $filters = [
            'owner_cnic' => $extracted['owner_cnic'],
            'owner_name' => $extracted['owner_name'],
            'property_number' => $extracted['property_number'],
        ];

        $matches = array_filter($filters, static fn ($v): bool => filled($v)) !== []
            ? Property::query()->publicSearch($filters)->latest()->limit(50)->get()
            : collect();

        if ($matches->count() === 1 && ($property = $matches->first()) !== null) {
            $verification = $action->handlePath($property, 'local', $path, $mime, $request->user());

            return to_route('verify-property.show', [$property, 'result' => $verification->id]);
        }

        return Inertia::render('public/Search', [
            'filters' => [...$filters, 'city' => null, 'province' => null],
            'hasSearched' => true,
            'scanned' => true,
            'extracted' => $extracted,
            'results' => $matches->map(fn (Property $p): array => $this->summary($p))->all(),
        ]);
    }

    /**
     * Verify a chosen property using the image already uploaded on the search page.
     */
    public function verifyScan(Request $request, Property $property, VerifyOwnershipDocument $action): RedirectResponse
    {
        abort_unless($property->status === PropertyStatus::Approved, 404);

        $scan = $request->session()->get('vp_scan');

        if (! is_array($scan)) {
            return to_route('verify-property.index')->with('error', 'Please upload your document again.');
        }

        $disk = (string) $scan['disk'];
        $path = (string) $scan['path'];

        if (! Storage::disk($disk)->exists($path)) {
            return to_route('verify-property.index')->with('error', 'Please upload your document again.');
        }

        $verification = $action->handlePath($property, $disk, $path, (string) $scan['mime'], $request->user());

        return to_route('verify-property.show', [$property, 'result' => $verification->id]);
    }

    public function show(Request $request, Property $property): Response
    {
        abort_unless($property->status === PropertyStatus::Approved, 404);

        $result = null;
        $resultId = $request->integer('result');

        if ($resultId) {
            $verification = PropertyVerification::query()
                ->where('property_id', $property->id)
                ->find($resultId);

            $result = $verification ? $this->verificationResult($verification) : null;
        }

        return Inertia::render('public/Verify', [
            'property' => $this->detail($property),
            'result' => $result,
        ]);
    }

    public function verify(VerifyOwnershipRequest $request, Property $property, VerifyOwnershipDocument $action): RedirectResponse
    {
        abort_unless($property->status === PropertyStatus::Approved, 404);

        $verification = $action->handle($property, $request->file('document'), $request->user());

        return to_route('verify-property.show', [$property, 'result' => $verification->id]);
    }

    /**
     * @return array<string, mixed>
     */
    private function summary(Property $property): array
    {
        return [
            'id' => $property->id,
            'property_number' => $property->property_number,
            'title' => $property->title,
            'type' => $property->type->label(),
            'owner_name' => $property->owner_name,
            'owner_cnic' => $this->maskCnic($property->owner_cnic),
            'city' => $property->city,
            'province' => $property->province,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function detail(Property $property): array
    {
        return [
            'id' => $property->id,
            'property_number' => $property->property_number,
            'title' => $property->title,
            'type' => $property->type->label(),
            'owner_name' => $property->owner_name,
            'owner_cnic' => $this->maskCnic($property->owner_cnic),
            'city' => $property->city,
            'province' => $property->province,
            'address' => $property->address,
            'area_label' => rtrim(rtrim((string) $property->area_value, '0'), '.').' '.$property->area_unit->label(),
            'approved_at' => $property->approved_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function verificationResult(PropertyVerification $verification): array
    {
        $ocr = $verification->ocr_data;

        return [
            'final_status' => [
                'value' => $verification->final_status->value,
                'label' => $verification->final_status->label(),
                'color' => $verification->final_status->color(),
            ],
            'score' => $ocr['score'] ?? null,
            'checks' => $ocr['checks'] ?? [],
            'notes' => $ocr['notes'] ?? null,
            'ai' => $verification->ai_result,
            'created_at' => $verification->created_at?->toIso8601String(),
        ];
    }

    private function maskCnic(string $cnic): string
    {
        $digits = (string) preg_replace('/\D/', '', $cnic);

        if (strlen($digits) < 4) {
            return str_repeat('•', strlen($digits));
        }

        return str_repeat('•', strlen($digits) - 4).substr($digits, -4);
    }
}
