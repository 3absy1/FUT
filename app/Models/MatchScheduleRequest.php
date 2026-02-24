<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MatchScheduleRequest extends Model
{
    protected $fillable = [
        'requested_by_user_id',
        'club_id',
        'opponent_club_id',
        'opponent_joined_by_user_id',
        'area_id',
        'requested_datetime',
        'matched_slot_id',
        'stadium_id',
        'match_id',
        'team_source',
        'status',
        'payment_status',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'requested_datetime' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class);
    }

    public function opponentClub(): BelongsTo
    {
        return $this->belongsTo(Club::class, 'opponent_club_id');
    }

    public function matchedSlot(): BelongsTo
    {
        return $this->belongsTo(MatchScheduleRequestSlot::class, 'matched_slot_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function stadium(): BelongsTo
    {
        return $this->belongsTo(Stadium::class);
    }

    public function slots(): HasMany
    {
        return $this->hasMany(MatchScheduleRequestSlot::class, 'match_schedule_request_id');
    }

    public function players(): HasMany
    {
        return $this->hasMany(MatchScheduleRequestPlayer::class, 'match_schedule_request_id');
    }
}
