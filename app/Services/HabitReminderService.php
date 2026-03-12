<?php

namespace App\Services;

use App\Models\Habit;
use App\Models\HabitReminder;
use App\Models\User;
use App\Repositories\Contracts\HabitReminderRepositoryContract;
use App\Services\Contracts\HabitReminderServiceContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Habit Reminder Service
 * Manages habit reminders and notifications
 */
class HabitReminderService implements HabitReminderServiceContract
{
    public function __construct(
        protected HabitReminderRepositoryContract $habitReminderRepository
    ) {}

    /**
     * Create reminder for habit
     */
    public function createReminder(User $user, Habit $habit, array $data): HabitReminder
    {
        $reminderData = [
            'user_id' => $user->id,
            'habit_id' => $habit->id,
            'time' => $data['time'],
            'days' => $data['days'] ?? null,
            'timezone' => $data['timezone'] ?? 'UTC',
            'is_enabled' => $data['is_enabled'] ?? true,
            'notification_type' => $data['notification_type'] ?? 'push',
            'message' => $data['message'] ?? null,
        ];

        $reminder = $this->habitReminderRepository->create($reminderData);

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
        return $this->habitReminderRepository->getAllForUser($user);
    }

    /**
     * Get reminders due for sending now
     */
    public function getDueReminders(): Collection
    {
        return $this->habitReminderRepository->getDueReminders()
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
        $this->habitReminderRepository->update($reminder, [
            'last_sent_at' => now(),
        ]);
    }

    /**
     * Update reminder
     */
    public function updateReminder(HabitReminder $reminder, array $data): bool
    {
        return $this->habitReminderRepository->update($reminder, $data);
    }

    /**
     * Delete reminder
     */
    public function deleteReminder(HabitReminder $reminder): bool
    {
        return $this->habitReminderRepository->delete($reminder);
    }
}
