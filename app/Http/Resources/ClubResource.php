<?php

namespace App\Http\Resources;

use App\Models\ClubMember;
use App\Models\GameMatch;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClubResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'icon' => $this->icon,
            'max_players' => $this->max_players,
            'rating' => (float) $this->rating,
            'exp' => (int) ($this->exp ?? 0),
            'total_wins' => $this->getTotalWins(),
            'win_rate' => $this->getWinRate(),
            'coach' => $this->getCoach(),
            'area' => new AreaResource($this->whenLoaded('area')),
        ];
    }

    private function getTotalWins(): int
    {
        return GameMatch::query()
            ->whereNotNull('result')
            ->where(function ($query) {
                $query
                    ->where(function ($subQuery) {
                        $subQuery
                            ->where('result', 'club_a')
                            ->where('club_a_id', $this->id);
                    })
                    ->orWhere(function ($subQuery) {
                        $subQuery
                            ->where('result', 'club_b')
                            ->where('club_b_id', $this->id);
                    });
            })
            ->count();
    }

    private function getWinRate(): float
    {
        $totalMatches = GameMatch::query()
            ->whereNotNull('result')
            ->where(function ($query) {
                $query
                    ->where('club_a_id', $this->id)
                    ->orWhere('club_b_id', $this->id);
            })
            ->count();

        if ($totalMatches === 0) {
            return 0.0;
        }

        return round(($this->getTotalWins() / $totalMatches) * 100, 2);
    }

    private function getCoach(): ?array
    {
        $coachMembership = ClubMember::query()
            ->with('user:id,name,nick_name,phone')
            ->where('club_id', $this->id)
            ->where('is_active', true)
            ->where('role', 'coach')
            ->latest('id')
            ->first();

        if (! $coachMembership) {
            $coachMembership = ClubMember::query()
                ->with('user:id,name,nick_name,phone')
                ->where('club_id', $this->id)
                ->where('is_active', true)
                ->where('role', 'captain')
                ->latest('id')
                ->first();
        }

        $coachUser = $coachMembership?->user;
        if (! $coachUser) {
            return null;
        }

        return [
            'id' => $coachUser->id,
            'name' => $coachUser->name,
            'nick_name' => $coachUser->nick_name,
            'phone' => $coachUser->phone,
        ];
    }
}

