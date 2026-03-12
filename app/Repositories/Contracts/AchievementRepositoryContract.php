<?php

namespace App\Repositories\Contracts;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Contract AchievementRepositoryContract
 */
interface AchievementRepositoryContract
{
    /**
     * Get all available achievements (excluding secret ones).
     */
    public function getAll(): Collection;

    /**
     * Get achievements not yet unlocked by the user.
     */
    public function getLockedForUser(User $user): Collection;

    /**
     * Unlock an achievement for a user.
     */
    public function unlockForUser(User $user, Achievement $achievement, array $metadata = []): bool;

    /**
     * Get all achievements unlocked by a user.
     */
    public function getUnlockedForUser(User $user): Collection;
}
