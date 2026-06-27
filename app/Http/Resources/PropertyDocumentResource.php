<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\PropertyDocument;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PropertyDocument
 */
class PropertyDocumentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => ['value' => $this->type->value, 'label' => $this->type->label()],
            'original_name' => $this->original_name,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'size_label' => $this->humanSize(),
            'is_image' => $this->isImage(),
            'file_hash' => $this->file_hash,
            'download_url' => route('properties.documents.show', [$this->property_id, $this->id]),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
