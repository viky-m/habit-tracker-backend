<?php

namespace App\Services\Contracts;

use App\Models\Habit;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface HabitLogServiceContract
{
    public function logCompletion(
        User $user,
        Habit $habit,
        array $data
    ): array;

    public function getLogsForHabit(Habit $habit): Collection;
}
