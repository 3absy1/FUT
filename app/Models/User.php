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
        'gk_diving',
        'gk_handling',
        'gk_kicking',
        'gk_reflexes',
        'gk_positioning',
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
            'gk_diving' => 'integer',
            'gk_handling' => 'integer',
            'gk_kicking' => 'integer',
            'gk_reflexes' => 'integer',
            'gk_positioning' => 'integer',
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

    public function overallRating(): ?int
    {
        $position = $this->position;

        if ($position === 'goal_keeper') {
            $goalkeeperStats = [
                'gk_diving' => $this->gk_diving,
                'gk_handling' => $this->gk_handling,
                'gk_kicking' => $this->gk_kicking,
                'gk_reflexes' => $this->gk_reflexes,
                'gk_positioning' => $this->gk_positioning,
            ];

            if (in_array(null, $goalkeeperStats, true)) {
                return null;
            }

            // FIFA-like GK OVR from dedicated keeper attributes.
            $overallGk = ((int) $goalkeeperStats['gk_diving'] * 0.21)
                + ((int) $goalkeeperStats['gk_handling'] * 0.21)
                + ((int) $goalkeeperStats['gk_kicking'] * 0.12)
                + ((int) $goalkeeperStats['gk_reflexes'] * 0.31)
                + ((int) $goalkeeperStats['gk_positioning'] * 0.15);

            return (int) round($overallGk);
        }

        $stats = [
            'pac' => $this->pac,
            'sho' => $this->sho,
            'pas' => $this->pas,
            'dri' => $this->dri,
            'def' => $this->def,
            'phy' => $this->phy,
        ];

        if (! $position || in_array(null, $stats, true)) {
            return null;
        }

        $weightsByPosition = [
            // FC/FIFA-like profile: pace+shooting+dribbling lead.
            'attacker' => ['pac' => 0.22, 'sho' => 0.28, 'pas' => 0.12, 'dri' => 0.24, 'def' => 0.04, 'phy' => 0.10],
            // Midfield profile: passing+dribbling+defense balance.
            'midfielder' => ['pac' => 0.14, 'sho' => 0.12, 'pas' => 0.27, 'dri' => 0.22, 'def' => 0.15, 'phy' => 0.10],
            // Defender profile: defending+physical dominate.
            'defender' => ['pac' => 0.12, 'sho' => 0.03, 'pas' => 0.15, 'dri' => 0.08, 'def' => 0.37, 'phy' => 0.25],
        ];

        $weights = $weightsByPosition[$position] ?? null;
        if (! $weights) {
            return null;
        }

        $overall = 0.0;
        foreach ($weights as $key => $weight) {
            $overall += ((int) $stats[$key]) * $weight;
        }

        return (int) round($overall);
    }
}
