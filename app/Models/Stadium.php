<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Stadium extends Model
{
    protected $fillable = [
        'name', 'whatsapp_number', 'location', 'area_id',
        'price_per_hour', 'icon', 'rating',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'float',
            'price_per_hour' => 'decimal:2',
        ];
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(GameMatch::class);
    }

    public function tournaments(): HasMany
    {
        return $this->hasMany(Tournament::class);
    }

    public function matchScheduleRequests(): HasMany
    {
        return $this->hasMany(MatchScheduleRequest::class);
    }

    public function pitches(): HasMany
    {
        return $this->hasMany(Pitch::class);
    }
}
