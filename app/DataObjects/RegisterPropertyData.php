<?php

declare(strict_types=1);

namespace App\DataObjects;

use App\Enums\AreaUnit;
use App\Enums\DocumentType;
use App\Enums\PropertyType;
use Illuminate\Http\UploadedFile;

final readonly class RegisterPropertyData
{
    /**
     * @param  list<DocumentUpload>  $documents
     */
    public function __construct(
        public string $propertyNumber,
        public string $title,
        public PropertyType $type,
        public ?string $description,
        public string $ownerName,
        public string $ownerCnic,
        public ?string $ownerContact,
        public string $address,
        public string $city,
        public string $province,
        public float $areaValue,
        public AreaUnit $areaUnit,
        public array $documents = [],
    ) {}

    /**
     * Build from already-validated request input.
     *
     * @param  array<string, mixed>  $validated
     * @param  array<int, array{file: UploadedFile, type: string}>  $documents
     */
    public static function fromValidated(array $validated, array $documents): self
    {
        return new self(
            propertyNumber: $validated['property_number'],
            title: $validated['title'],
            type: PropertyType::from($validated['type']),
            description: $validated['description'] ?? null,
            ownerName: $validated['owner_name'],
            ownerCnic: $validated['owner_cnic'],
            ownerContact: $validated['owner_contact'] ?? null,
            address: $validated['address'],
            city: $validated['city'],
            province: $validated['province'],
            areaValue: (float) $validated['area_value'],
            areaUnit: AreaUnit::from($validated['area_unit']),
            documents: array_values(array_map(
                static fn (array $doc): DocumentUpload => new DocumentUpload(
                    file: $doc['file'],
                    type: DocumentType::from($doc['type']),
                ),
                $documents,
            )),
        );
    }

    /**
     * The persistable property attributes (excludes documents).
     *
     * @return array<string, mixed>
     */
    public function toAttributes(): array
    {
        return [
            'property_number' => $this->propertyNumber,
            'title' => $this->title,
            'type' => $this->type,
            'description' => $this->description,
            'owner_name' => $this->ownerName,
            'owner_cnic' => $this->ownerCnic,
            'owner_contact' => $this->ownerContact,
            'address' => $this->address,
            'city' => $this->city,
            'province' => $this->province,
            'area_value' => $this->areaValue,
            'area_unit' => $this->areaUnit,
        ];
    }
}
