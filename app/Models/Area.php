<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    protected $fillable = ['name', 'coordinates'];

    protected function casts(): array
    {
        return [
            'name' => 'array',
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

    public function clubs(): HasMany
    {
        return $this->hasMany(Club::class);
    }

    public function stadiums(): HasMany
    {
        return $this->hasMany(Stadium::class);
    }
}
