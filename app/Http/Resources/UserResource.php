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
        $positionStats = $this->position === 'goal_keeper'
            ? [
                'gk_diving' => $this->gk_diving,
                'gk_handling' => $this->gk_handling,
                'gk_kicking' => $this->gk_kicking,
                'gk_reflexes' => $this->gk_reflexes,
                'gk_positioning' => $this->gk_positioning,
            ]
            : [
                'pac' => $this->pac,
                'sho' => $this->sho,
                'pas' => $this->pas,
                'dri' => $this->dri,
                'def' => $this->def,
                'phy' => $this->phy,
            ];

        return array_merge([
            'id' => $this->id,
            'name' => $this->name,
            'nick_name' => $this->nick_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'age' => $this->birth_date?->age,
            'is_verified' => $this->is_verified,
            'rating' => $this->rating,
            'wallet_balance' => $this->wallet_balance ?? 0,
            'exp' => $this->exp ?? 0,
            'position' => $this->position,
            'overall_rating' => method_exists($this->resource, 'overallRating')
                ? $this->overallRating()
                : null,
            'goals_scored' => $this->goals_scored ?? 0,
            'assists_count' => $this->assists_count ?? 0,
            'friends_count' => Friend::query()->where('user_id', $this->id)->count(),
            'division' => $division ? [
                'id' => $division->id,
                'name' => $division->name[app()->getLocale()] ?? $division->name['en'] ?? null,
                'matches_count' => $division->matches_count,
                'checkpoints' => $division->checkpoints,
                'sort_order' => $division->sort_order,
                'current_match' => (int) ($this->division_current_match ?? 0),
                'last_checkpoint_match' => (int) ($this->division_last_checkpoint_match ?? 0),
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
        ], $positionStats);
    }
}
