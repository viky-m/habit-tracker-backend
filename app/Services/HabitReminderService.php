<?php

namespace App\Services;

use App\Models\Habit;
use App\Models\HabitReminder;
use App\Models\User;
use App\Services\Contracts\HabitReminderServiceContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Habit Reminder Service
 * Manages habit reminders and notifications
 */
class HabitReminderService implements HabitReminderServiceContract
{
    /**
     * Create reminder for habit
     */
    public function createReminder(User $user, Habit $habit, array $data): HabitReminder
    {
        $reminder = HabitReminder::create([
            'user_id' => $user->id,
            'habit_id' => $habit->id,
            'time' => $data['time'],
            'days' => $data['days'] ?? null,
            'timezone' => $data['timezone'] ?? 'UTC',
            'is_enabled' => $data['is_enabled'] ?? true,
            'notification_type' => $data['notification_type'] ?? 'push',
            'message' => $data['message'] ?? null,
        ]);

        Log::info('Reminder created', [
            'user_id' => $user->id,
            'habit_id' => $habit->id,
            'time' => $data['time'],
        ]);

        return $reminder;
    }

    /**
     * Get user's reminders
     */
    public function getUserReminders(User $user): Collection
    {
        return HabitReminder::where('user_id', $user->id)
            ->with('habit')
            ->orderBy('time')
            ->get();
    }

    /**
     * Get reminders due for sending now
     */
    public function getDueReminders(): Collection
    {
        $now = now();
        $currentTime = $now->format('H:i');

        return HabitReminder::where('is_enabled', true)
            ->whereTime('time', '<=', $currentTime)
            ->with(['user', 'habit'])
            ->get()
            ->filter(function ($reminder) {
                // Check if already sent today
                if ($reminder->last_sent_at && $reminder->last_sent_at->isToday()) {
                    return false;
                }

                // Check if should send today (day of week)
                return $reminder->shouldSendToday();
            });
    }

    /**
     * Mark reminder as sent
     */
    public function markAsSent(HabitReminder $reminder): void
    {
        $reminder->update([
            'last_sent_at' => now(),
        ]);
    }
}

