<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => 'required|string|max:255',
            'key_description' => 'sometimes|string|max:500',
            'group' => 'required|string|max:255',
            'group_description' => 'sometimes|string|max:500',
            'language_code' => 'required|string|size:2',
            'value' => 'required|string',
            'tags' => 'sometimes|array',
            'tags.*' => 'string|max:255',
        ];
    }
}