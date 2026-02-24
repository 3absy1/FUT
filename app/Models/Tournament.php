<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tournament extends Model
{
    protected $fillable = [
        'name', 'stadium_id', 'start_date', 'end_date',
        'max_teams', 'entry_fee_per_team', 'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'entry_fee_per_team' => 'decimal:2',
        ];
    }

    public function stadium(): BelongsTo
    {
        return $this->belongsTo(Stadium::class);
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
