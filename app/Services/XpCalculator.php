<?php

namespace App\Services;

use App\Models\Habit;
use App\Services\Contracts\XpCalculatorContract;

/**
 * XP Calculator Service
 * Single Responsibility: Calculate XP
 * Open/Closed: Can be extended with new XP strategies
 */
class XpCalculator implements XpCalculatorContract
{
    private const BASE_XP = 10;

    private const STREAK_BONUS_THRESHOLD = 3;

    private const STREAK_BONUS_PERCENTAGE = 0.2;

    private const XP_PER_LEVEL = 100;

    private const LEVEL_EXPONENT = 1.5;

    private const LEVEL_MULTIPLIER = 20;

    /**
     * Calculate XP based on habit and streak
     */
    public function calculate(Habit $habit): int
    {
        $xp = self::BASE_XP;

        // Add streak bonus
        $xp += $this->calculateStreakBonus($habit->streak);

        return (int) $xp;
    }

    /**
     * Calculate streak bonus
     */
    private function calculateStreakBonus(int $streak): float
    {
        if ($streak <= self::STREAK_BONUS_THRESHOLD) {
            return 0;
        }

        $bonusTiers = floor($streak / self::STREAK_BONUS_THRESHOLD);

        return $bonusTiers * (self::BASE_XP * self::STREAK_BONUS_PERCENTAGE);
    }

    /**
     * Get XP required for specific level
     * Exponential curve: 100, 220, 360, 520, 700...
     */
    public function getXpForLevel(int $level): int
    {
        return self::XP_PER_LEVEL * $level + (int) (pow($level, self::LEVEL_EXPONENT) * self::LEVEL_MULTIPLIER);
    }
}

