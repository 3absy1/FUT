<?php

namespace App\Http\Requests\MatchScheduleRequest;

use Illuminate\Foundation\Http\FormRequest;

class StadiumAcceptMatchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }
}

