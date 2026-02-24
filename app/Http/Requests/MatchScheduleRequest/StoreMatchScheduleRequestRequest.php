<?php

namespace App\Http\Requests\MatchScheduleRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreMatchScheduleRequestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'club_id' => ['required', 'integer', 'exists:clubs,id'],
            'area_id' => ['nullable', 'integer', 'exists:areas,id'],

            'team_source' => ['nullable', 'in:club,friends'],

            'player_user_ids' => ['nullable', 'array', 'max:4'],
            'player_user_ids.*' => ['integer', 'distinct', 'exists:users,id'],

            'schedule_slots' => ['required', 'array', 'min:1', 'max:10'],
            'schedule_slots.*.start_datetime' => ['required', 'date'],
            'schedule_slots.*.end_datetime' => ['required', 'date'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $slots = $this->input('schedule_slots', []);
            foreach ($slots as $i => $slot) {
                $start = $slot['start_datetime'] ?? null;
                $end = $slot['end_datetime'] ?? null;
                if (! $start || ! $end) {
                    continue;
                }

                try {
                    $startDt = \Carbon\Carbon::parse($start);
                    $endDt = \Carbon\Carbon::parse($end);
                } catch (\Throwable) {
                    continue;
                }

                if ($endDt->lessThanOrEqualTo($startDt)) {
                    $validator->errors()->add("schedule_slots.$i.end_datetime", __('api.match_schedule_request.slot_end_after_start'));
                }
            }
        });
    }
}

