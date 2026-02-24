<?php

namespace App\Http\Requests\Stadium;

use Illuminate\Foundation\Http\FormRequest;

class StoreStadiumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'whatsapp_number' => 'required|string|max:20',
            'location' => 'required|string',
            'area_id' => 'required|exists:areas,id',
            'price_per_hour' => 'required|numeric|min:0',
            'icon' => 'nullable|string',
            'rating' => 'nullable|numeric|min:0|max:5',
        ];
    }
}
