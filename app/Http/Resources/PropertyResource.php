<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Property
 */
class PropertyResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'property_number' => $this->property_number,
            'title' => $this->title,
            'type' => ['value' => $this->type->value, 'label' => $this->type->label()],
            'description' => $this->description,

            'owner_name' => $this->owner_name,
            'owner_cnic' => $this->owner_cnic,
            'owner_contact' => $this->owner_contact,

            'address' => $this->address,
            'city' => $this->city,
            'province' => $this->province,
            'area_value' => $this->area_value,
            'area_unit' => ['value' => $this->area_unit->value, 'label' => $this->area_unit->label()],
            'area_label' => rtrim(rtrim((string) $this->area_value, '0'), '.').' '.$this->area_unit->label(),

            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->label(),
                'color' => $this->status->color(),
            ],

            'created_by' => $this->whenLoaded('createdBy', fn () => $this->createdBy?->name),
            'approved_by' => $this->whenLoaded('approvedBy', fn () => $this->approvedBy?->name),
            'approved_at' => $this->approved_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),

            'block' => $this->whenLoaded(
                'block',
                fn () => $this->block
                    ? (new PropertyBlockResource($this->block))->resolve($request)
                    : null,
            ),
        ];
    }
}
