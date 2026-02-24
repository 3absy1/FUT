<?php

namespace App\Http\Requests\Stadium;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStadiumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'whatsapp_number' => 'sometimes|string|max:20',
            'location' => 'sometimes|string',
            'area_id' => 'sometimes|exists:areas,id',
            'price_per_hour' => 'sometimes|numeric|min:0',
            'icon' => 'sometimes|string',
            'rating' => 'sometimes|numeric|min:0|max:5',
        ];
    }
}
