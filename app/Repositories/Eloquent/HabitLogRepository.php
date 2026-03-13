<?php

namespace App\Repositories\Eloquent;

use App\Models\Habit;
use App\Models\HabitLog;
use App\Models\User;
use App\Repositories\Contracts\HabitLogRepositoryContract;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class HabitLogRepository
 */
class HabitLogRepository implements HabitLogRepositoryContract
{
    /**
     * {@inheritDoc}
     */
    public function updateOrCreate(array $attributes, array $values): HabitLog
    {
        return HabitLog::updateOrCreate($attributes, $values);
    }

    /**
     * {@inheritDoc}
     */
    public function getLogsForHabit(Habit $habit): Collection
    {
        return $habit->logs()->orderBy('completed_at', 'desc')->get();
    }

    /**
     * {@inheritDoc}
     */
    public function countForUser(User $user): int
    {
        return $user->habitLogs()->count();
    }

    /**
     * {@inheritDoc}
     */
    public function countForUserInRange(User $user, int $days): int
    {
        return $user->habitLogs()
            ->where('completed_at', '>=', now()->subDays($days))
            ->count();
    }
}
