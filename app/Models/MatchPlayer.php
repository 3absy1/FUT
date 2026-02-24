<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchPlayer extends Model
{
    protected $fillable = ['match_id', 'user_id', 'club_id', 'is_playing'];

    protected function casts(): array
    {
        return ['is_playing' => 'boolean'];
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(GameMatch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }
}
