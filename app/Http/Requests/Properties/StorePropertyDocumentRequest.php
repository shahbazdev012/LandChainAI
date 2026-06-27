<?php

declare(strict_types=1);

namespace App\Http\Requests\Properties;

use App\DataObjects\DocumentUpload;
use App\Enums\DocumentType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePropertyDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('property')) ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(DocumentType::class)],
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file.mimes' => 'Documents must be a JPG, PNG or PDF file.',
            'file.max' => 'The document may not be larger than 10 MB.',
        ];
    }

    public function toUpload(): DocumentUpload
    {
        return new DocumentUpload(
            file: $this->file('file'),
            type: DocumentType::from($this->validated('type')),
        );
    }
}
