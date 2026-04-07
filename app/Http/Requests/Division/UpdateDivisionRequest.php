<?php

namespace App\Http\Requests\Division;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDivisionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $name = $this->input('name');
        if (is_string($name) && $name !== '') {
            $this->merge([
                'name' => ['en' => $name, 'ar' => $name],
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'array'],
            'name.en' => ['required_with:name', 'string', 'max:255'],
            'name.ar' => ['required_with:name', 'string', 'max:255'],
            'matches_count' => ['sometimes', 'integer', 'min:1'],
            'checkpoints' => ['sometimes', 'nullable', 'array'],
            'checkpoints.*' => ['integer', 'min:1'],
            'sort_order' => ['sometimes', 'integer', 'min:1'],
        ];
    }
}

