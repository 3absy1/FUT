<?php

namespace App\Http\Requests\Club;

use Illuminate\Foundation\Http\FormRequest;

class StoreClubRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'max_players' => ['nullable', 'integer', 'min:1', 'max:50'],
            'rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'area_id' => ['nullable', 'integer', 'exists:areas,id'],
        ];
    }
}

