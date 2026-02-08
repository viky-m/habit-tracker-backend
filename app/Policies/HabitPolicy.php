<?php

namespace App\Policies;

use App\Models\Habit;
use App\Models\User;

class HabitPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Habit $habit): bool
    {
        return $habit->user_id === $user->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Habit $habit): bool
    {
        return $habit->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Habit $habit): bool
    {
        return $habit->user_id === $user->id;
    }
}
