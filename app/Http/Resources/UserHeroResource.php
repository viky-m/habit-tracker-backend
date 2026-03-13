<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserHeroResource extends JsonResource
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
            'hero' => new HeroResource($this->whenLoaded('hero')),
            'level' => $this->level,
            'experience' => $this->experience,
            'experience_to_next_level' => $this->experience_to_next_level,
            'level_progress' => round($this->getLevelProgress(), 2),
            'total_habits_completed' => $this->total_habits_completed,
            'current_streak' => $this->current_streak,
            'best_streak' => $this->best_streak,
            'is_active' => $this->is_active,
            'is_unlocked' => $this->is_unlocked,
            'customization' => $this->customization,
            'stats' => $this->stats,
            'achievements' => $this->achievements,
            'last_active_at' => $this->last_active_at?->toISOString(),
        ];
    }
}
