<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'age',
        'rating',
        'wallet_balance',
        'area_id',
        'is_stadium_owner',
        'stadium_id',
        'exp',
        'fcm_token',
        'is_notification',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
            'rating' => 'float',
            'wallet_balance' => 'decimal:2',
            'area_id' => 'integer',
            'is_stadium_owner' => 'boolean',
            'stadium_id' => 'integer',
            'exp' => 'integer',
            'is_notification' => 'boolean',
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

    /**
     * Get the level tier for the user's current exp.
     */
    public function currentLevel(): ?Level
    {
        return Level::forExp((int) $this->exp);
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
