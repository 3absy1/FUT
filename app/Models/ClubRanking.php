<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClubRanking extends Model
{
    protected $table = 'club_ranking';

    protected $fillable = [
        'club_id', 'tournament_id',
        'matches_played', 'matches_won', 'rank',
    ];

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }
}
