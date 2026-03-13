<?php

namespace App\Repositories\Contracts;

use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contract HabitLogRepositoryContract
 */
interface HabitLogRepositoryContract
{
    /**
     * Create or update a habit log.
     */
    public function updateOrCreate(array $attributes, array $values): HabitLog;

    /**
     * Get logs for a specific habit.
     */
    public function getLogsForHabit(Habit $habit): Collection;

    /**
     * Count total logs for a user.
     */
    public function countForUser(User $user): int;

    /**
     * Count logs for a user within a date range.
     */
    public function countForUserInRange(User $user, int $days): int;
}
