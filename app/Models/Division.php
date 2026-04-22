<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $table = 'levels';

    protected $fillable = ['name', 'matches_count', 'checkpoints', 'exp_win', 'draw_exp', 'sort_order'];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'matches_count' => 'integer',
            'checkpoints' => 'array',
            'exp_win' => 'integer',
            'draw_exp' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function getLocalizedNameAttribute(): ?string
    {
        $i18n = $this->name;
        if (is_string($i18n)) {
            return $i18n;
        }
        if (!is_array($i18n) || $i18n === []) {
            return null;
        }

        $locale = app()->getLocale();
        return $i18n[$locale] ?? $i18n['en'] ?? $i18n['ar'] ?? null;
    }
}

