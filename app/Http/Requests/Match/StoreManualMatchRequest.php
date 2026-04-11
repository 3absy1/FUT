<?php

namespace App\Http\Requests\Match;

use App\Models\Pitch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreManualMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'club_a_id' => ['required', 'integer', 'exists:clubs,id'],
            'club_b_id' => ['required', 'integer', 'exists:clubs,id', 'different:club_a_id'],
            'pitch_id' => [
                'required',
                'integer',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $user = $this->user();
                    if (! $user || ! $user->stadium_id) {
                        $fail(__('api.pitch.invalid_for_stadium'));

                        return;
                    }
                    $ok = Pitch::query()
                        ->whereKey((int) $value)
                        ->where('stadium_id', $user->stadium_id)
                        ->exists();
                    if (! $ok) {
                        $fail(__('api.pitch.invalid_for_stadium'));
                    }
                },
            ],
            'scheduled_datetime' => ['required', 'date'],
            'status' => ['sometimes', 'string', Rule::in(['scheduled', 'pending', 'ongoing'])],
            'score_club_a' => ['sometimes', 'integer', 'min:0'],
            'score_club_b' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
