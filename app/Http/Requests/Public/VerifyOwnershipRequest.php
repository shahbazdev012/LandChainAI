<?php

declare(strict_types=1);

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOwnershipRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // public — anyone may attempt verification
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'document' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'document.required' => 'Please choose your ownership document to upload.',
            'document.mimes' => 'The document must be a JPG, PNG or PDF file.',
            'document.max' => 'The document may not be larger than 10 MB.',
        ];
    }
}
