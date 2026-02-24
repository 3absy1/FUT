<?php

namespace App\Http\Requests\MatchScheduleRequest;

use Illuminate\Foundation\Http\FormRequest;

class JoinMatchScheduleRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'opponent_club_id' => ['required', 'integer', 'exists:clubs,id'],
            'slot_id' => ['required', 'integer'],
            'team_source' => ['nullable', 'in:club,friends'],
            'player_user_ids' => ['nullable', 'array', 'max:4'],
            'player_user_ids.*' => ['integer', 'distinct', 'exists:users,id'],
        ];
    }
}

