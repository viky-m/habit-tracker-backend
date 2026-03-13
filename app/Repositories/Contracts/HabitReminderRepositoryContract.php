<?php

namespace App\Repositories\Contracts;

use App\Models\HabitReminder;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Contract HabitReminderRepositoryContract
 */
interface HabitReminderRepositoryContract
{
    /**
     * Get all reminders for a user.
     */
    public function getAllForUser(User $user): Collection;

    /**
     * Create a new reminder.
     */
    public function create(array $data): HabitReminder;

    /**
     * Update an existing reminder.
     */
    public function update(HabitReminder $reminder, array $data): bool;

    /**
     * Delete a reminder.
     */
    public function delete(HabitReminder $reminder): bool;

    /**
     * Get reminders due for execution.
     */
    public function getDueReminders(): Collection;
}
