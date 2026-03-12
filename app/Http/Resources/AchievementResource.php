<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource transformer for a single Achievement model.
 *
 * Used in both the full achievement list and the user's unlocked achievements collection.
 */
class AchievementResource extends JsonResource
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
            'key' => $this->key,
            'title' => $this->title,
            'description' => $this->description,
            'icon' => $this->icon,
            'category' => $this->category,
            'rarity' => $this->rarity,
            'xp_reward' => $this->xp_reward,
            'unlocked_at' => $this->whenPivotLoaded('user_achievements', function () {
                return $this->pivot->created_at;
            }),
        ];
    }
}
