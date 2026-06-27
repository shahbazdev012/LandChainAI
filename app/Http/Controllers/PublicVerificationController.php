<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\PropertyBlock;
use App\Services\HashChain\HashChainService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Public, unauthenticated property authenticity check. Looks a property up by
 * its registration number or block hash and reports its verification status
 * and hash-chain integrity, without exposing private registry data.
 */
class PublicVerificationController extends Controller
{
    public function __invoke(Request $request, HashChainService $chain): Response
    {
        $request->validate(['query' => ['nullable', 'string', 'max:100']]);

        $term = $request->string('query')->trim()->toString();

        if ($term === '') {
            return Inertia::render('verify/Index', ['result' => null, 'query' => '']);
        }

        $property = $this->lookup($term);

        if ($property === null) {
            return Inertia::render('verify/Index', [
                'query' => $term,
                'result' => ['found' => false],
            ]);
        }

        $property->load('block');

        return Inertia::render('verify/Index', [
            'query' => $term,
            'result' => [
                'found' => true,
                'property' => $this->publicProperty($property),
                'block' => $property->block ? [
                    'sequence' => $property->block->sequence,
                    'hash' => $property->block->hash,
                    'previous_hash' => $property->block->previous_hash,
                    'created_at' => $property->block->created_at?->toIso8601String(),
                ] : null,
                'block_valid' => $property->block ? $chain->isHashValid($property->block) : null,
                'chain_intact' => $chain->verify()->intact,
            ],
        ]);
    }

    private function lookup(string $term): ?Property
    {
        $property = Property::query()->where('property_number', $term)->first();

        if ($property === null && preg_match('/^[a-f0-9]{64}$/i', $term)) {
            $property = PropertyBlock::query()->where('hash', $term)->first()?->property;
        }

        return $property;
    }

    /**
     * Public-safe projection — owner CNIC is masked, contact is withheld.
     *
     * @return array<string, mixed>
     */
    private function publicProperty(Property $property): array
    {
        return [
            'property_number' => $property->property_number,
            'title' => $property->title,
            'type' => $property->type->label(),
            'owner_name' => $property->owner_name,
            'owner_cnic' => $this->maskCnic($property->owner_cnic),
            'city' => $property->city,
            'province' => $property->province,
            'area_label' => rtrim(rtrim((string) $property->area_value, '0'), '.').' '.$property->area_unit->label(),
            'status' => [
                'value' => $property->status->value,
                'label' => $property->status->label(),
                'color' => $property->status->color(),
            ],
            'verified_at' => $property->verified_at?->toIso8601String(),
            'registered_at' => $property->created_at?->toIso8601String(),
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
