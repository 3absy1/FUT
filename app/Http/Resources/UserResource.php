<?php

namespace App\Http\Resources;

use App\Models\Friend;
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
        $division = method_exists($this->resource, 'currentDivision')
            ? $this->currentDivision()
            : null;
        $level = $this->currentLevel();

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
            'wallet_balance' => $this->wallet_balance,
            'exp' => $this->exp,
            'friends_count' => Friend::query()->where('user_id', $this->id)->count(),
            'stadium' => $this->when(
                $this->relationLoaded('stadium') && $this->stadium,
                fn () => new StadiumResource($this->stadium)
            ),
            'division' => $division ? [
                'id' => $division->id,
                'name' => $division->name,
                'matches_count' => $division->matches_count,
                'checkpoints' => $division->checkpoints,
                'sort_order' => $division->sort_order,
            ] : null,
            // 'level' => $level ? [
            //     'id' => $level->id,
            //     'name' => $level->name,
            //     'matches_count' => $level->matches_count,
            //     'checkpoints' => $level->checkpoints,
            //     'sort_order' => $level->sort_order,
            // ] : null,
            'fcm_token' => $this->fcm_token,
            'is_notification' => $this->is_notification,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
