<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Club extends Model
{
    protected $fillable = [
        'name', 'icon', 'max_players', 'rating', 'exp', 'area_id',
    ];

    protected function casts(): array
    {
        return ['rating' => 'float', 'exp' => 'integer'];
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(ClubMember::class);
    }

    public function activeMembers(): HasMany
    {
        return $this->members()->where('is_active', true);
    }

    public function tournamentParticipants(): HasMany
    {
        return $this->hasMany(TournamentParticipant::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function matchScheduleRequests(): HasMany
    {
        return $this->hasMany(MatchScheduleRequest::class);
    }

    public function matchPlayers(): HasMany
    {
        return $this->hasMany(MatchPlayer::class);
    }

    public function playerRankings(): HasMany
    {
        return $this->hasMany(PlayerRanking::class);
    }

    public function clubRankings(): HasMany
    {
        return $this->hasMany(ClubRanking::class);
    }

    public function matchesAsClubA(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'club_a_id');
    }

    public function matchesAsClubB(): HasMany
    {
        return $this->hasMany(GameMatch::class, 'club_b_id');
    }
}
