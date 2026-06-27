<?php

declare(strict_types=1);

namespace App\Http\Requests\Properties;

use App\DataObjects\RegisterPropertyData;
use App\Enums\AreaUnit;
use App\Enums\DocumentType;
use App\Enums\PropertyType;
use App\Models\Property;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;

class StorePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Property::class) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'property_number' => ['required', 'string', 'max:50', Rule::unique('properties', 'property_number')],
            'title' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(PropertyType::class)],
            'description' => ['nullable', 'string', 'max:2000'],

            'owner_name' => ['required', 'string', 'max:255'],
            'owner_cnic' => ['required', 'string', 'max:20', 'regex:/^[0-9\- ]{5,20}$/'],
            'owner_contact' => ['nullable', 'string', 'max:50'],

            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:120'],
            'province' => ['required', 'string', 'max:120'],
            'area_value' => ['required', 'numeric', 'min:0.01', 'max:9999999999'],
            'area_unit' => ['required', Rule::enum(AreaUnit::class)],

            'documents' => ['nullable', 'array', 'max:10'],
            'documents.*.type' => ['required_with:documents.*.file', Rule::enum(DocumentType::class)],
            'documents.*.file' => ['required_with:documents.*.type', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'owner_cnic.regex' => 'The owner CNIC may only contain digits, spaces and dashes.',
            'documents.*.file.mimes' => 'Documents must be a JPG, PNG or PDF file.',
            'documents.*.file.max' => 'Each document may not be larger than 10 MB.',
        ];
    }

    public function toDto(): RegisterPropertyData
    {
        return RegisterPropertyData::fromValidated($this->validated(), $this->normalizeDocuments());
    }

    /**
     * Flatten the nested `documents[i][file|type]` payload into a simple list.
     *
     * @return array<int, array{file: UploadedFile, type: string}>
     */
    private function normalizeDocuments(): array
    {
        $documents = [];

        foreach ((array) $this->file('documents', []) as $index => $document) {
            if (isset($document['file'])) {
                $documents[] = [
                    'file' => $document['file'],
                    'type' => (string) $this->input("documents.{$index}.type"),
                ];
            }
        }

        return $documents;
    }
}
