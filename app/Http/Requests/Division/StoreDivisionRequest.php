<?php

namespace App\Http\Requests\Division;

use Illuminate\Foundation\Http\FormRequest;

class StoreDivisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'matches_count' => ['required', 'integer', 'min:1'],
            'checkpoints' => ['nullable', 'array'],
            'checkpoints.*' => ['integer', 'min:1'],
            'sort_order' => ['required', 'integer', 'min:1'],
        ];
    }
}

