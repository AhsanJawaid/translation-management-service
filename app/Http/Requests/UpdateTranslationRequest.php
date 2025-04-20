<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => 'sometimes|string|max:255',
            'key_description' => 'sometimes|string|max:500',
            'group' => 'sometimes|string|max:255',
            'group_description' => 'sometimes|string|max:500',
            'language_code' => 'sometimes|string|size:2',
            'value' => 'sometimes|string',
            'tags' => 'sometimes|array',
            'tags.*' => 'string|max:255',
        ];
    }
}