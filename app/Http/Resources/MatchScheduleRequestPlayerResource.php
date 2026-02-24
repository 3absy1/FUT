<?php

namespace App\Http\Resources;

use App\Models\MatchScheduleRequestPlayer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MatchScheduleRequestPlayer
 */
class MatchScheduleRequestPlayerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'team' => $this->team,
            'role' => $this->role,
            'source' => $this->source,
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}

