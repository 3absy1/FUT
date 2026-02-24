<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
            'age' => ['required', 'integer', 'min:1', 'max:120'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'fcm_token' => ['nullable', 'string'],
        ];
    }
}
