<?php

namespace App\Http\Requests\Club;

use Illuminate\Foundation\Http\FormRequest;

class ClubMemberSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'min:1', 'max:255'],
        ];
    }
}

