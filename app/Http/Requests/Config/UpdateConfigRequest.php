<?php

namespace App\Http\Requests\Config;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $config = $this->route('config');
        $configId = is_object($config) ? $config->id : null;

        return [
            'key' => ['sometimes', 'string', 'max:255', Rule::unique('configs', 'key')->ignore($configId)],
            'value' => ['sometimes', 'nullable', 'string'],
        ];
    }
}

