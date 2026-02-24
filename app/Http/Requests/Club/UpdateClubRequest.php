<?php

namespace App\Http\Requests\Club;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClubRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'icon' => ['sometimes', 'nullable', 'string', 'max:255'],
            'max_players' => ['sometimes', 'integer', 'min:1', 'max:50'],
            'rating' => ['sometimes', 'numeric', 'min:0', 'max:5'],
            'area_id' => ['sometimes', 'nullable', 'integer', 'exists:areas,id'],
        ];
    }
}

