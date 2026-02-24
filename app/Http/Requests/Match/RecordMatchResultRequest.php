<?php

namespace App\Http\Requests\Match;

use Illuminate\Foundation\Http\FormRequest;

class RecordMatchResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'winner' => ['required', 'in:club_a,club_b,draw'],
            'score_club_a' => ['required', 'integer', 'min:0'],
            'score_club_b' => ['required', 'integer', 'min:0'],
        ];
    }
}

