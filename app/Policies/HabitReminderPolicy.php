<?php

namespace App\Policies;

use App\Models\HabitReminder;
use App\Models\User;

class HabitReminderPolicy
{
    /**
     * Determine if user can update the reminder
     */
    public function update(User $user, HabitReminder $reminder): bool
    {
        return $reminder->user_id === $user->id;
    }

    /**
     * Determine if user can delete the reminder
     */
    public function delete(User $user, HabitReminder $reminder): bool
    {
        return $reminder->user_id === $user->id;
    }
}
