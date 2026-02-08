<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HabitReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'habit_id',
        'user_id',
        'time',
        'days',
        'timezone',
        'is_enabled',
        'notification_type',
        'message',
        'last_sent_at',
    ];

    protected function casts(): array
    {
        return [
            'days' => 'array',
            'is_enabled' => 'boolean',
            'last_sent_at' => 'datetime',
        ];
    }

    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if reminder should be sent today
     */
    public function shouldSendToday(): bool
    {
        if (! $this->is_enabled) {
            return false;
        }

        // If no specific days set, send every day
        if (empty($this->days)) {
            return true;
        }

        // Check if today is in the days array (1=Mon, 7=Sun)
        $today = now($this->timezone)->dayOfWeekIso;

        return in_array($today, $this->days);
    }
}
