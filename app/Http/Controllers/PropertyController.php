<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Properties\RegisterProperty;
use App\Enums\AreaUnit;
use App\Enums\DocumentType;
use App\Enums\PropertyStatus;
use App\Enums\PropertyType;
use App\Http\Requests\Properties\StorePropertyRequest;
use App\Http\Requests\Properties\UpdatePropertyRequest;
use App\Http\Resources\PropertyResource;
use App\Models\Property;
use App\Services\HashChain\HashChainService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PropertyController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Property::class);

        $properties = Property::query()
            ->with('latestVerification')
            ->search($request->string('search')->toString())
            ->status($request->string('status')->toString())
            ->when(
                $request->filled('type'),
                fn ($query) => $query->where('type', $request->string('type')->toString()),
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('properties/Index', [
            'properties' => PropertyResource::collection($properties),
            'filters' => [
                'search' => $request->string('search')->toString(),
                'status' => $request->string('status')->toString(),
                'type' => $request->string('type')->toString(),
            ],
            'statusOptions' => PropertyStatus::options(),
            'typeOptions' => PropertyType::options(),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Property::class);

        return Inertia::render('properties/Create', [
            'typeOptions' => PropertyType::options(),
            'areaUnitOptions' => AreaUnit::options(),
            'documentTypeOptions' => DocumentType::options(),
        ]);
    }

    public function store(StorePropertyRequest $request, RegisterProperty $action): RedirectResponse
    {
        $property = $action->handle($request->toDto(), $request->user());

        return to_route('properties.show', $property)
            ->with('success', "Property {$property->property_number} registered and sealed into the chain.");
    }

    public function show(Property $property, HashChainService $chain): Response
    {
        $this->authorize('view', $property);

        $property->load(['registeredBy', 'documents', 'block', 'verifications.runBy', 'latestVerification']);

        return Inertia::render('properties/Show', [
            'property' => (new PropertyResource($property))->resolve(),
            'chain' => $chain->verify()->toArray(),
            'blockValid' => $property->block ? $chain->isHashValid($property->block) : null,
            'documentTypeOptions' => DocumentType::options(),
        ]);
    }

    public function edit(Property $property): Response
    {
        $this->authorize('update', $property);

        return Inertia::render('properties/Edit', [
            'property' => (new PropertyResource($property))->resolve(),
            'typeOptions' => PropertyType::options(),
            'areaUnitOptions' => AreaUnit::options(),
        ]);
    }

    public function update(UpdatePropertyRequest $request, Property $property): RedirectResponse
    {
        $property->update($request->validated());

        return to_route('properties.show', $property)
            ->with('success', 'Property details updated.');
    }

    public function destroy(Property $property): RedirectResponse
    {
        $this->authorize('delete', $property);

        $property->delete();

        return to_route('properties.index')
            ->with('success', 'Property removed from the registry.');
    }
}
