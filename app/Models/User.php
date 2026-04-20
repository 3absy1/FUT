<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'nick_name',
        'email',
        'phone',
        'password',
        'otp',
        'otp_expires_at',
        'is_verified',
        'birth_date',
        'rating',
        'wallet_balance',
        'area_id',
        'is_stadium_owner',
        'stadium_id',
        'exp',
        'division_id',
        'division_current_match',
        'division_last_checkpoint_match',
        'fcm_token',
        'is_notification',
        'position',
        'pac',
        'sho',
        'pas',
        'dri',
        'def',
        'phy',
        'goals_scored',
        'assists_count',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    protected $casts = [
        'name' => 'array',
        'checkpoints' => 'array',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
            'birth_date' => 'date',
            'rating' => 'float',
            'wallet_balance' => 'decimal:2',
            'area_id' => 'integer',
            'is_stadium_owner' => 'boolean',
            'stadium_id' => 'integer',
            'exp' => 'integer',
            'division_id' => 'integer',
            'division_current_match' => 'integer',
            'division_last_checkpoint_match' => 'integer',
            'is_notification' => 'boolean',
            'pac' => 'integer',
            'sho' => 'integer',
            'pas' => 'integer',
            'dri' => 'integer',
            'def' => 'integer',
            'phy' => 'integer',
            'goals_scored' => 'integer',
            'assists_count' => 'integer',
        ];
    }

    public function clubMembers(): HasMany
    {
        return $this->hasMany(ClubMember::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(UserNotification::class);
    }

    public function matchPlayers(): HasMany
    {
        return $this->hasMany(MatchPlayer::class);
    }

    public function matchScheduleRequests(): HasMany
    {
        return $this->hasMany(MatchScheduleRequest::class, 'requested_by_user_id');
    }

    public function playerRankings(): HasMany
    {
        return $this->hasMany(PlayerRanking::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function stadium(): BelongsTo
    {
        return $this->belongsTo(Stadium::class, 'stadium_id');
    }

    /**
     * Backward compatibility for older clients/code.
     */
    public function currentLevel(): ?Level
    {
        if (! $this->division_id) {
            return null;
        }
        return Level::query()->find($this->division_id);
    }

    public function currentDivision(): ?Division
    {
        return $this->division;
    }

    public function acceptsDummyOtp(string $code): bool
    {
        if ($code === '1111') {
            return true;
        }
        if (! $this->otp || $this->otp_expires_at?->isPast()) {
            return false;
        }
        return $this->otp === $code;
    }
}
