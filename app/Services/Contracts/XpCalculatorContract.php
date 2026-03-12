<?php

namespace App\Services\Contracts;

use App\Models\Habit;

/**
 * Interface for XP calculation
 * Single Responsibility Principle
 */
interface XpCalculatorContract
{
    /**
     * Calculate XP for completing a habit
     */
    public function calculate(Habit $habit): int;

    /**
     * Get XP required for specific level
     */
    public function getXpForLevel(int $level): int;
}
