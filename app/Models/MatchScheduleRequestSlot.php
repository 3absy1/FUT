<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchScheduleRequestSlot extends Model
{
    protected $fillable = [
        'match_schedule_request_id',
        'start_datetime',
        'end_datetime',
    ];

    protected function casts(): array
    {
        return [
            'start_datetime' => 'datetime',
            'end_datetime' => 'datetime',
        ];
    }

    public function matchScheduleRequest(): BelongsTo
    {
        return $this->belongsTo(MatchScheduleRequest::class);
    }
}

