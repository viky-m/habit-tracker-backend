<?php

namespace App\Services;

use App\Models\UserHero;
use App\Services\Contracts\LevelUpServiceContract;
use App\Services\Contracts\XpCalculatorContract;
use Illuminate\Support\Facades\Log;

/**
 * Level Up Service
 * Single Responsibility: Handle level up logic
 * Dependency Inversion: Depends on XpCalculatorContract abstraction
 */
class LevelUpService implements LevelUpServiceContract
{
    public function __construct(
        private XpCalculatorContract $xpCalculator
    ) {}

    /**
     * Check and apply level up if hero has enough XP
     */
    public function processLevelUp(UserHero $hero): bool
    {
        $leveledUp = false;
        $xpNeeded = $this->xpCalculator->getXpForLevel($hero->level + 1);

        while ($hero->experience >= $xpNeeded) {
            $hero->level++;
            $leveledUp = true;
            $xpNeeded = $this->xpCalculator->getXpForLevel($hero->level + 1);

            $this->logLevelUp($hero);
        }

        return $leveledUp;
    }

    /**
     * Log level up event
     */
    private function logLevelUp(UserHero $hero): void
    {
        Log::info('Hero leveled up!', [
            'hero_id' => $hero->id,
            'new_level' => $hero->level,
            'experience' => $hero->experience,
        ]);
    }
}

