<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchScheduleRequestPlayer extends Model
{
    protected $fillable = [
        'match_schedule_request_id',
        'user_id',
        'team',
        'role',
        'source',
    ];

    public function matchScheduleRequest(): BelongsTo
    {
        return $this->belongsTo(MatchScheduleRequest::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

