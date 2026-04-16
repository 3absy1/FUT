<?php

namespace App\Http\Resources;

use App\Models\Friend;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StadiumOwnerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'nick_name' => $this->nick_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'age' => $this->birth_date?->age,
            'is_verified' => $this->is_verified,
            'is_stadium_owner' => (bool) $this->is_stadium_owner,
            'stadium_id' => $this->stadium_id,
            'rating' => $this->rating,
            'wallet_balance' => $this->wallet_balance ?? 0,
            'exp' => $this->exp ?? 0,
            'friends_count' => Friend::query()->where('user_id', $this->id)->count(),
            'stadium' => $this->when(
                $this->relationLoaded('stadium') && $this->stadium,
                fn () => new StadiumResource($this->stadium)
            ),
            'fcm_token' => $this->fcm_token,
            'is_notification' => $this->is_notification,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}

