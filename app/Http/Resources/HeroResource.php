<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HeroResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'model_url' => $this->model_url,
            'thumbnail_url' => $this->thumbnail_url,
            'rarity' => $this->rarity,
            'unlock_level' => $this->unlock_level,
            'unlock_cost' => $this->unlock_cost,
            'is_premium' => $this->is_premium,
            'stats' => $this->stats,
            'customization_options' => $this->customization_options,
        ];
    }
}
