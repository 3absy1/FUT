<?php

namespace App\Http\Requests\Club;

use Illuminate\Foundation\Http\FormRequest;

class InviteClubMembersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_ids'   => ['required', 'array', 'min:1'],
            'user_ids.*' => ['integer', 'distinct', 'exists:users,id'],
            'role'       => ['required', 'string', 'in:player,coach,captain'],
        ];
    }
}

