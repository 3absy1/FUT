<?php

namespace App\Http\Resources;

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
            'area' => new AreaResource($this->whenLoaded('area')),
        ];
    }
}

