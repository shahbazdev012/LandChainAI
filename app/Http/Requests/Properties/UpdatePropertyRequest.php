<?php

declare(strict_types=1);

namespace App\Http\Requests\Properties;

use App\Enums\AreaUnit;
use App\Enums\PropertyType;
use App\Models\Property;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePropertyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->property()) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'property_number' => ['required', 'string', 'max:50', Rule::unique('properties', 'property_number')->ignore($this->property())],
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
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'owner_cnic.regex' => 'The owner CNIC may only contain digits, spaces and dashes.',
        ];
    }

    private function property(): Property
    {
        $property = $this->route('property');

        abort_unless($property instanceof Property, 404);

        return $property;
    }
}
