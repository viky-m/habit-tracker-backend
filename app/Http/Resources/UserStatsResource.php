<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource transformer for the user's overall statistics.
 *
 * Returns a flat aggregate object covering habits, streaks, hero, and achievements.
 */
class UserStatsResource extends JsonResource
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
            'habits' => $this->resource['habits'] ?? [],
            'streaks' => $this->resource['streaks'] ?? [],
            'hero' => $this->resource['hero'] ?? null,
            'achievements' => $this->resource['achievements'] ?? [],
        ];
    }
}
