<?php

namespace App\Http\Resources;

use App\Models\MatchScheduleRequestSlot;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MatchScheduleRequestSlot
 */
class MatchScheduleRequestSlotResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'start_datetime' => $this->start_datetime?->toIso8601String(),
            'end_datetime' => $this->end_datetime?->toIso8601String(),
        ];
    }
}

