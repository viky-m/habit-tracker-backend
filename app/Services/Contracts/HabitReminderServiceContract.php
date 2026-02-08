<?php

namespace App\Services\Contracts;

use App\Models\Habit;
use App\Models\HabitReminder;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Interface for habit reminder service
 */
interface HabitReminderServiceContract
{
    /**
     * Create reminder for habit
     */
    public function createReminder(User $user, Habit $habit, array $data): HabitReminder;

    /**
     * Get user's reminders
     */
    public function getUserReminders(User $user): Collection;

    /**
     * Get reminders due for sending now
     */
    public function getDueReminders(): Collection;

    /**
     * Mark reminder as sent
     */
    public function markAsSent(HabitReminder $reminder): void;
}

