<?php

namespace App\Services\Contracts;

use App\Models\UserHero;

/**
 * Interface for level up mechanics
 * Single Responsibility Principle
 */
interface LevelUpServiceContract
{
    /**
     * Check and apply level up if hero has enough XP
     */
    public function processLevelUp(UserHero $hero): bool;
}

