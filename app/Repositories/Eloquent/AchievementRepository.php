<?php

namespace App\Repositories\Eloquent;

use App\Models\Achievement;
use App\Models\User;
use App\Repositories\Contracts\AchievementRepositoryContract;
use Illuminate\Support\Collection;

/**
 * Class AchievementRepository
 */
class AchievementRepository implements AchievementRepositoryContract
{
    /**
     * {@inheritDoc}
     */
    public function getAll(): Collection
    {
        return Achievement::where('is_secret', false)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getLockedForUser(User $user): Collection
    {
        return Achievement::whereNotIn('id', function ($query) use ($user) {
            $query->select('achievement_id')
                ->from('user_achievements')
                ->where('user_id', $user->id);
        })->get();
    }

    /**
     * {@inheritDoc}
     */
    public function unlockForUser(User $user, Achievement $achievement, array $metadata = []): bool
    {
        return $user->achievements()->syncWithoutDetaching([
            $achievement->id => [
                'unlocked_at' => now(),
                'metadata' => json_encode($metadata),
                'is_notified' => false,
            ],
        ])['attached'] !== [];
    }

    /**
     * {@inheritDoc}
     */
    public function getUnlockedForUser(User $user): Collection
    {
        return $user->achievements()
            ->withPivot(['unlocked_at', 'metadata', 'is_notified'])
            ->orderBy('user_achievements.unlocked_at', 'desc')
            ->get();
    }
}
