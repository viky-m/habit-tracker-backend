<?php

namespace App\Repositories\Eloquent;

use App\Models\Habit;
use App\Models\User;
use App\Repositories\Contracts\HabitRepositoryContract;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class HabitRepository
 */
class HabitRepository implements HabitRepositoryContract
{
    /**
     * {@inheritDoc}
     */
    public function getAllForUser(User $user, array $filters = []): Collection
    {
        $query = $user->habits();

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query->get();
    }

    /**
     * {@inheritDoc}
     */
    public function findById(int $id): ?Habit
    {
        return Habit::find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function create(User $user, array $data): Habit
    {
        return $user->habits()->create($data);
    }

    /**
     * {@inheritDoc}
     */
    public function update(Habit $habit, array $data): bool
    {
        return $habit->update($data);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(Habit $habit): bool
    {
        return $habit->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function updateStats(Habit $habit, array $stats): bool
    {
        return $habit->update($stats);
    }

    /**
     * {@inheritDoc}
     */
    public function countForUser(User $user): int
    {
        return $user->habits()->count();
    }

    /**
     * {@inheritDoc}
     */
    public function getMaxStreakForUser(User $user): int
    {
        return (int) $user->habits()->max('streak');
    }
}
