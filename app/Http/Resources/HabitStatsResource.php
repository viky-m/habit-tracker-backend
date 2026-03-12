<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource transformer for individual habit statistics.
 *
 * Returns a flat statistics object with no data wrapper.
 */
class HabitStatsResource extends JsonResource
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
            'total_completions' => $this->resource['total_completions'] ?? 0,
            'current_streak' => $this->resource['current_streak'] ?? 0,
            'best_streak' => $this->resource['best_streak'] ?? 0,
            'completion_rate_30_days' => $this->resource['completion_rate_30_days'] ?? 0,
            'last_completed_at' => $this->resource['last_completed_at'] ?? null,
            'is_completed_today' => $this->resource['is_completed_today'] ?? false,
        ];
    }
}
