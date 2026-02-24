<?php

namespace App\Http\Resources;

use App\Models\Friendship;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Friendship
 */
class FriendshipRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'requested_by_user_id' => $this->requested_by_user_id,
            'user_id' => $this->user_id,
            'friend_id' => $this->friend_id,
            'accepted_at' => $this->accepted_at?->toIso8601String(),
            'rejected_at' => $this->rejected_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'requested_by' => new UserResource($this->whenLoaded('requestedBy')),
            'user' => new UserResource($this->whenLoaded('user')),
            'friend' => new UserResource($this->whenLoaded('friend')),
        ];
    }
}

