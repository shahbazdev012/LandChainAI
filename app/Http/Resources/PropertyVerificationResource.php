<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\PropertyVerification;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PropertyVerification
 */
class PropertyVerificationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->label(),
                'color' => $this->status->color(),
            ],
            'score' => $this->score,
            'checks' => $this->checks,
            'extracted' => $this->extracted,
            'notes' => $this->notes,
            'document_id' => $this->document_id,
            'run_by' => $this->whenLoaded('runBy', fn () => $this->runBy?->name),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
