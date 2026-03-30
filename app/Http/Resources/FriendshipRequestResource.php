<?php

namespace App\Http\Resources;

use App\Models\FriendRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin FriendRequest
 */
class FriendshipRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'sender_user_id' => $this->sender_user_id,
            'receiver_user_id' => $this->receiver_user_id,
            'accepted_at' => $this->accepted_at?->toIso8601String(),
            'rejected_at' => $this->rejected_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'sender' => new UserResource($this->whenLoaded('sender')),
            'receiver' => new UserResource($this->whenLoaded('receiver')),
        ];
    }
}

