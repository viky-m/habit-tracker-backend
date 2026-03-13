<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Resource transformer for a single HabitReminder model.
 *
 * Exposes the reminder schedule fields (time, days, timezone, etc.) for the API consumer.
 */
class HabitReminderResource extends JsonResource
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
            'time' => $this->time,
            'days' => $this->days,
            'timezone' => $this->timezone,
            'is_enabled' => $this->is_enabled,
            'notification_type' => $this->notification_type,
            'message' => $this->message,
        ];
    }
}
