<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tournament extends Model
{
    protected $fillable = [
        'name', 'stadium_id', 'min_division_id', 'start_date', 'end_date',
        'max_teams', 'entry_fee_per_team', 'status',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
            'entry_fee_per_team' => 'decimal:2',
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

    public function stadium(): BelongsTo
    {
        return $this->belongsTo(Stadium::class);
    }

    public function minDivision(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'min_division_id');
    }

    public function matches(): HasMany
    {
        return $this->hasMany(GameMatch::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(TournamentParticipant::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function playerRankings(): HasMany
    {
        return $this->hasMany(PlayerRanking::class);
    }

    public function clubRankings(): HasMany
    {
        return $this->hasMany(ClubRanking::class);
    }
}
