<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Properties\StorePropertyDocumentRequest;
use App\Models\Property;
use App\Models\PropertyDocument;
use App\Services\Documents\DocumentStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PropertyDocumentController extends Controller
{
    public function store(StorePropertyDocumentRequest $request, Property $property, DocumentStorage $storage): RedirectResponse
    {
        $storage->store($property, $request->toUpload(), $request->user());

        return back()->with('success', 'Document uploaded.');
    }

    public function show(Property $property, PropertyDocument $document): StreamedResponse
    {
        $this->authorize('view', $property);
        $this->ensureBelongsTo($property, $document);

        return Storage::disk($document->disk)
            ->download($document->path, $document->original_name);
    }

    public function destroy(Property $property, PropertyDocument $document, DocumentStorage $storage): RedirectResponse
    {
        $this->authorize('update', $property);
        $this->ensureBelongsTo($property, $document);

        $storage->delete($document);

        return back()->with('success', 'Document removed.');
    }

    private function ensureBelongsTo(Property $property, PropertyDocument $document): void
    {
        abort_unless($document->property_id === $property->id, 404);
    }
}
