<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameMatch extends Model
{
    protected $table = 'matches';

    protected $fillable = [
        'club_a_id', 'club_b_id', 'stadium_id', 'scheduled_datetime',
        'status', 'score_club_a', 'score_club_b', 'tournament_id',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_datetime' => 'datetime',
            'score_club_a' => 'integer',
            'score_club_b' => 'integer',
        ];
    }

    public function clubA(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'club_a_id');
    }

    public function clubB(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'club_b_id');
    }

    public function stadium(): BelongsTo
    {
        return $this->belongsTo(Stadium::class);
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function matchPlayers(): HasMany
    {
        return $this->hasMany(MatchPlayer::class, 'match_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
