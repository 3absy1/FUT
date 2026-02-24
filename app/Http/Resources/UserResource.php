<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array. Add more keys here as the app grows.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $level = $this->currentLevel();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'nick_name' => $this->nick_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'age' => $this->age,
            'is_verified' => $this->is_verified,
            'rating' => $this->rating,
            'wallet_balance' => $this->wallet_balance,
            'exp' => $this->exp,
            'level' => $level ? [
                'id' => $level->id,
                'name' => $level->name,
                'min_points' => $level->min_points,
                'max_points' => $level->max_points,
                'sort_order' => $level->sort_order,
            ] : null,
            'fcm_token' => $this->fcm_token,
            'is_notification' => $this->is_notification,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
