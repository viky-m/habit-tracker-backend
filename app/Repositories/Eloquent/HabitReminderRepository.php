<?php

namespace App\Repositories\Eloquent;

use App\Models\HabitReminder;
use App\Models\User;
use App\Repositories\Contracts\HabitReminderRepositoryContract;
use Illuminate\Support\Collection;

/**
 * Class HabitReminderRepository
 */
class HabitReminderRepository implements HabitReminderRepositoryContract
{
    /**
     * {@inheritDoc}
     */
    public function getAllForUser(User $user): Collection
    {
        return HabitReminder::where('user_id', $user->id)
            ->with(['habit'])
            ->orderBy('time')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): HabitReminder
    {
        return HabitReminder::create($data);
    }

    /**
     * {@inheritDoc}
     */
    public function update(HabitReminder $reminder, array $data): bool
    {
        return $reminder->update($data);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(HabitReminder $reminder): bool
    {
        return $reminder->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function getDueReminders(): Collection
    {
        $currentTime = now()->format('H:i');

        return HabitReminder::where('is_enabled', true)
            ->where('time', '<=', $currentTime)
            ->with(['user', 'habit'])
            ->get();
    }
}
