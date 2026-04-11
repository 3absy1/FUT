<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StadiumResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'whatsapp_number' => $this->whatsapp_number,
            'location' => $this->location,

            'price_per_hour' => (float) $this->price_per_hour,
            'rating' => (float) $this->rating,

            'icon' => $this->icon,

            'area' => new AreaResource($this->whenLoaded('area')),

            'pitches' => PitchResource::collection($this->whenLoaded('pitches')),

            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
