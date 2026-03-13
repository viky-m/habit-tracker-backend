<?php

namespace App\Services\Contracts;

use App\Models\Habit;
use App\Models\User;
use App\Models\UserHero;

/**
 * Interface for gamification service
 * Following Interface Segregation Principle
 */
interface GamificationServiceContract
{
    /**
     * Award XP to user's active hero
     */
    public function awardXpForHabit(User $user, Habit $habit): array;

    /**
     * Update habit streak
     */
    public function updateStreak(Habit $habit): array;

    /**
     * Create first hero for new user
     */
    public function createFirstHero(User $user): UserHero;
}
