<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'nick_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|nullable|email|unique:users,email,' . auth()->id(),
            'birth_date' => 'sometimes|date|before:today|after:1900-01-01',
            'fcm_token' => 'sometimes|nullable|string',
            'area_id' => 'sometimes|nullable|exists:areas,id',
            'position' => 'sometimes|in:attacker,defender,midfielder,goal_keeper',
            'pac' => 'sometimes|integer|min:1|max:99',
            'sho' => 'sometimes|integer|min:1|max:99',
            'pas' => 'sometimes|integer|min:1|max:99',
            'dri' => 'sometimes|integer|min:1|max:99',
            'def' => 'sometimes|integer|min:1|max:99',
            'phy' => 'sometimes|integer|min:1|max:99',
            'goals_scored' => 'sometimes|integer|min:0',
            'assists_count' => 'sometimes|integer|min:0',
        ];
    }
}
