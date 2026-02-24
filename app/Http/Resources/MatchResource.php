<?php

namespace App\Http\Resources;

use App\Models\GameMatch;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin GameMatch
 */
class MatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'club_a_id' => $this->club_a_id,
            'club_b_id' => $this->club_b_id,
            'stadium_id' => $this->stadium_id,
            'scheduled_datetime' => $this->scheduled_datetime?->toIso8601String(),
            'status' => $this->status,
            'score_club_a' => $this->score_club_a,
            'score_club_b' => $this->score_club_b,

            'club_a' => new ClubResource($this->whenLoaded('clubA')),
            'club_b' => new ClubResource($this->whenLoaded('clubB')),
            'stadium' => new StadiumResource($this->whenLoaded('stadium')),
        ];
    }
}

