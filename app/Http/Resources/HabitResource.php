<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HabitResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'icon' => $this->icon,
            'color' => $this->color,
            'frequency' => $this->frequency,
            'frequency_days' => $this->frequency_days,
            'target_count' => $this->target_count,
            'streak' => $this->streak,
            'best_streak' => $this->best_streak,
            'total_completions' => $this->total_completions,
            'is_active' => $this->is_active,
            'is_completed_today' => $this->isCompletedToday(),
            'last_completed_at' => $this->last_completed_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
