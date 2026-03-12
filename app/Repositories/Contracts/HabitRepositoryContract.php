<?php

namespace App\Repositories\Contracts;

use App\Models\Habit;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contract HabitRepositoryContract
 */
interface HabitRepositoryContract
{
    /**
     * Get all habits for a specific user.
     */
    public function getAllForUser(User $user, array $filters = []): Collection;

    /**
     * Find a habit by ID.
     */
    public function findById(int $id): ?Habit;

    /**
     * Create a new habit.
     */
    public function create(User $user, array $data): Habit;

    /**
     * Update an existing habit.
     */
    public function update(Habit $habit, array $data): bool;

    /**
     * Delete a habit.
     */
    public function delete(Habit $habit): bool;

    /**
     * Update habit statistics.
     */
    public function updateStats(Habit $habit, array $stats): bool;

    /**
     * Count total habits for a user.
     */
    public function countForUser(User $user): int;

    /**
     * Get the maximum streak for a user.
     */
    public function getMaxStreakForUser(User $user): int;
}
