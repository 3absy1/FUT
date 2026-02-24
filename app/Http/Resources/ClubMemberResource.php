<?php

namespace App\Http\Resources;

use App\Models\ClubMember;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin ClubMember
 */
class ClubMemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'club_id' => $this->club_id,
            'user_id' => $this->user_id,
            'role' => $this->role,
            'is_active' => (bool) $this->is_active,
            'user' => new UserResource($this->whenLoaded('user')),
            'club' => new ClubResource($this->whenLoaded('club')),
        ];
    }
}

