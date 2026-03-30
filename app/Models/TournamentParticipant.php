<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TournamentParticipant extends Model
{
    protected $fillable = [
        'tournament_id',
        'club_id',
        'division_id',
        'current_match',
        'last_checkpoint_match',
        'total_score',
        'rank',
    ];

    protected function casts(): array
    {
        return [
            'current_match' => 'integer',
            'last_checkpoint_match' => 'integer',
            'total_score' => 'integer',
            'rank' => 'integer',
        ];
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'division_id');
    }
}
