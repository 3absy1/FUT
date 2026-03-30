<?php

namespace App\Http\Requests\Division;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDivisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'matches_count' => ['sometimes', 'integer', 'min:1'],
            'checkpoints' => ['sometimes', 'nullable', 'array'],
            'checkpoints.*' => ['integer', 'min:1'],
            'sort_order' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}

