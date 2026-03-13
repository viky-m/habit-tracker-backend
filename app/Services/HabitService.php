<?php

namespace App\Services;

use App\Models\Habit;
use App\Models\User;
use App\Repositories\Contracts\HabitRepositoryContract;
use App\Services\Contracts\HabitServiceContract;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class HabitService
 */
class HabitService implements HabitServiceContract
{
    /**
     * HabitService constructor.
     */
    public function __construct(
        protected HabitRepositoryContract $habitRepository
    ) {}

    /**
     * Get all habits for the given user.
     */
    public function getHabitsForUser(User $user, array $filters = []): Collection
    {
        return $this->habitRepository->getAllForUser($user, $filters);
    }

    /**
     * Get habit by ID.
     */
    public function getHabitById(int $id): ?Habit
    {
        return $this->habitRepository->findById($id);
    }

    /**
     * Create a new habit for the user.
     */
    public function createHabit(User $user, array $data): Habit
    {
        return $this->habitRepository->create($user, $data);
    }

    /**
     * Update an existing habit.
     */
    public function updateHabit(Habit $habit, array $data): bool
    {
        return $this->habitRepository->update($habit, $data);
    }

    /**
     * Delete a habit.
     */
    public function deleteHabit(Habit $habit): bool
    {
        return $this->habitRepository->delete($habit);
    }

    /**
     * Get statistics for a habit.
     */
    public function getHabitStats(Habit $habit): array
    {
        return [
            'total_completions' => $habit->total_completions,
            'current_streak' => $habit->streak,
            'best_streak' => $habit->best_streak,
            'completion_rate_30_days' => round($habit->getCompletionRate(30), 1),
            'last_completed_at' => $habit->last_completed_at,
            'is_completed_today' => $habit->isCompletedToday(),
        ];
    }
}
