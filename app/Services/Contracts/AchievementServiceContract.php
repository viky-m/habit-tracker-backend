<?php

namespace App\Services\Contracts;

use App\Models\Achievement;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Interface for achievement service
 */
interface AchievementServiceContract
{
    /**
     * Check and unlock achievements for user
     */
    public function checkAchievements(User $user): Collection;

    /**
     * Unlock specific achievement for user
     */
    public function unlockAchievement(User $user, Achievement $achievement, array $metadata = []): void;

    /**
     * Get user's unlocked achievements
     */
    public function getUserAchievements(User $user): Collection;

    /**
     * Get all available achievements
     */
    public function getAllAchievements(): Collection;
}

