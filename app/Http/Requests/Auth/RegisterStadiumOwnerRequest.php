<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterStadiumOwnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'nick_name' => ['required', 'string', 'max:255', 'unique:users,nick_name'],
            'phone' => ['required', 'string', 'max:30', 'unique:users,phone'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'birth_date' => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'fcm_token' => ['nullable', 'string'],
            'stadium_id' => ['required', 'integer', 'exists:stadiums,id'],
        ];
    }
}
