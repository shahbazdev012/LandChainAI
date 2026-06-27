<?php

declare(strict_types=1);

namespace App\Actions\Properties;

use App\DataObjects\RegisterPropertyData;
use App\Enums\PropertyStatus;
use App\Models\Property;
use App\Models\User;
use App\Services\Documents\DocumentStorage;
use App\Services\HashChain\HashChainService;
use Illuminate\Support\Facades\DB;

/**
 * Registers a new property: persists the record, stores its documents, and
 * seals it into the blockchain-inspired hash chain — atomically.
 */
class RegisterProperty
{
    public function __construct(
        private readonly DocumentStorage $documents,
        private readonly HashChainService $chain,
    ) {}

    public function handle(RegisterPropertyData $data, User $registrar): Property
    {
        return DB::transaction(function () use ($data, $registrar): Property {
            $property = Property::query()->create([
                ...$data->toAttributes(),
                'status' => PropertyStatus::Pending,
                'registered_by' => $registrar->id,
            ]);

            foreach ($data->documents as $upload) {
                $this->documents->store($property, $upload, $registrar);
            }

            $this->chain->append($property);

            return $property;
        });
    }
}
