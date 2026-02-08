<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HabitLogResource extends JsonResource
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
            'habit_id' => $this->habit_id,
            'completed_at' => $this->completed_at?->toDateString(),
            'note' => $this->note,
            'count' => $this->count,
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}
