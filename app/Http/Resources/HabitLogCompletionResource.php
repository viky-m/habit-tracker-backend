<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource transformer for a habit log completion response.
 *
 * Bundles the created log entry, gamification updates, and newly unlocked achievements.
 */
class HabitLogCompletionResource extends JsonResource
{
    public static $wrap = false;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => new HabitLogResource($this->resource['log']),
            'gamification' => $this->resource['gamification'] ?? [],
            'achievements' => [
                'newly_unlocked' => collect($this->resource['achievements']['newly_unlocked'] ?? [])->map(fn ($a) => [
                    'id' => $a->id,
                    'title' => $a->title,
                    'icon' => $a->icon,
                    'xp_reward' => $a->xp_reward,
                ]),
                'count' => $this->resource['achievements']['count'] ?? 0,
            ],
        ];
    }
}
