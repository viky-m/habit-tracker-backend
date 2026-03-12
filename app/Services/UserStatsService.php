<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\AchievementRepositoryContract;
use App\Repositories\Contracts\HabitLogRepositoryContract;
use App\Repositories\Contracts\HabitRepositoryContract;
use App\Repositories\Contracts\UserHeroRepositoryContract;
use App\Services\Contracts\UserStatsServiceContract;
use App\Services\Contracts\XpCalculatorContract;

/**
 * Class UserStatsService
 */
class UserStatsService implements UserStatsServiceContract
{
    /**
     * UserStatsService constructor.
     */
    public function __construct(
        protected HabitRepositoryContract $habitRepository,
        protected HabitLogRepositoryContract $habitLogRepository,
        protected UserHeroRepositoryContract $userHeroRepository,
        protected AchievementRepositoryContract $achievementRepository,
        protected XpCalculatorContract $xpCalculator
    ) {}

    /**
     * Get overall statistics for a user.
     */
    public function getOverallStats(User $user): array
    {
        $habits = $this->habitRepository->getAllForUser($user);
        $activeHabitsCount = $habits->where('is_active', true)->count();

        // Habits statistics
        $habitsStats = [
            'total' => $habits->count(),
            'active' => $activeHabitsCount,
            'completed_today' => $habits->filter(fn ($h) => $h->isCompletedToday())->count(),
            'completion_rate_7_days' => $this->calculateCompletionRate($user, 7, $activeHabitsCount),
            'completion_rate_30_days' => $this->calculateCompletionRate($user, 30, $activeHabitsCount),
            'total_completions' => $this->habitLogRepository->countForUser($user),
        ];

        // Streaks statistics
        $streaksStats = [
            'longest_current' => $habits->max('streak') ?? 0,
            'longest_ever' => $habits->max('best_streak') ?? 0,
            'total_streak_days' => $habits->sum('streak'),
        ];

        // Active hero statistics
        $activeHero = $this->userHeroRepository->getActiveHero($user);
        $heroStats = null;

        if ($activeHero) {
            $nextLevelXp = $this->xpCalculator->getXpForLevel($activeHero->level + 1);
            $progress = $nextLevelXp > 0 ? ($activeHero->experience / $nextLevelXp) * 100 : 0;

            $heroStats = [
                'name' => $activeHero->hero->name ?? 'Unknown',
                'level' => $activeHero->level,
                'experience' => $activeHero->experience,
                'next_level_xp' => $nextLevelXp,
                'progress_percent' => round($progress, 1),
            ];
        }

        // Achievements statistics
        $unlockedAchievements = $this->achievementRepository->getUnlockedForUser($user);
        $achievementsStats = [
            'total_unlocked' => $unlockedAchievements->count(),
            'recent' => $unlockedAchievements->take(5),
        ];

        return [
            'habits' => $habitsStats,
            'streaks' => $streaksStats,
            'hero' => $heroStats,
            'achievements' => $achievementsStats,
        ];
    }

    /**
     * Calculate completion rate for last N days.
     */
    private function calculateCompletionRate(User $user, int $days, int $activeHabitsCount): float
    {
        if ($activeHabitsCount === 0) {
            return 0.0;
        }

        $expectedCompletions = $activeHabitsCount * $days;
        $actualCompletions = $this->habitLogRepository->countForUserInRange($user, $days);

        return round(($actualCompletions / $expectedCompletions) * 100, 1);
    }
}
