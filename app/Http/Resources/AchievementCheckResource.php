<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource transformer for the achievement check result.
 *
 * Returns the list of newly unlocked achievements and their count.
 */
class AchievementCheckResource extends JsonResource
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
            'newly_unlocked' => $this->resource['newly_unlocked'] ?? [],
            'count' => $this->resource['count'] ?? 0,
        ];
    }
}
