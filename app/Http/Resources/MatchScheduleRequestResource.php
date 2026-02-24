<?php

namespace App\Http\Resources;

use App\Models\MatchScheduleRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MatchScheduleRequest
 */
class MatchScheduleRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'club_id' => $this->club_id,
            'area_id' => $this->area_id,
            'stadium_id' => $this->stadium_id,
            'requested_by_user_id' => $this->requested_by_user_id,
            'opponent_club_id' => $this->opponent_club_id,
            'opponent_joined_by_user_id' => $this->opponent_joined_by_user_id,
            'matched_slot_id' => $this->matched_slot_id,
            'match_id' => $this->match_id,
            'team_source' => $this->team_source,
            'status' => $this->status,
            'payment_status' => $this->payment_status,
            'requested_datetime' => $this->requested_datetime?->toIso8601String(),
            'approved_at' => $this->approved_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),

            'club' => new ClubResource($this->whenLoaded('club')),
            'opponent_club' => new ClubResource($this->whenLoaded('opponentClub')),
            'area' => new AreaResource($this->whenLoaded('area')),
            'stadium' => new StadiumResource($this->whenLoaded('stadium')),
            'requested_by' => new UserResource($this->whenLoaded('requestedBy')),

            'players' => MatchScheduleRequestPlayerResource::collection($this->whenLoaded('players')),
            'slots' => MatchScheduleRequestSlotResource::collection($this->whenLoaded('slots')),
            'matched_slot' => new MatchScheduleRequestSlotResource($this->whenLoaded('matchedSlot')),
        ];
    }
}

