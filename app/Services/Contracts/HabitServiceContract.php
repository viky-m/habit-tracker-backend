<?php

namespace App\Services\Contracts;

use App\Models\Habit;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface HabitServiceContract
{
    public function getHabitsForUser(User $user, array $filters = []): Collection;

    public function getHabitById(int $id): ?Habit;

    public function createHabit(User $user, array $data): Habit;

    public function updateHabit(Habit $habit, array $data): bool;

    public function deleteHabit(Habit $habit): bool;

    public function getHabitStats(Habit $habit): array;
}
