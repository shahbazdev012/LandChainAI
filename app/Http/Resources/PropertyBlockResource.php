<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\PropertyBlock;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin PropertyBlock
 */
class PropertyBlockResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'sequence' => $this->sequence,
            'hash' => $this->hash,
            'previous_hash' => $this->previous_hash,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
