<?php

declare(strict_types=1);

namespace App\Services\Documents;

use App\DataObjects\DocumentUpload;
use App\Models\Property;
use App\Models\PropertyDocument;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

/**
 * Persists uploaded property documents to a private disk and records their
 * metadata. Files are never placed on a public disk.
 */
class DocumentStorage
{
    private const string DISK = 'local';

    public function store(Property $property, DocumentUpload $upload, ?User $uploadedBy = null): PropertyDocument
    {
        $file = $upload->file;
        $fileHash = hash_file('sha256', $file->getRealPath());

        $path = $file->store("properties/{$property->id}/documents", self::DISK);

        return $property->documents()->create([
            'type' => $upload->type,
            'disk' => self::DISK,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'file_hash' => $fileHash,
            'uploaded_by' => $uploadedBy?->id,
        ]);
    }

    public function delete(PropertyDocument $document): void
    {
        Storage::disk($document->disk)->delete($document->path);
        $document->delete();
    }
}
