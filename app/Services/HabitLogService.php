<?php

namespace App\Services;

use App\Models\Habit;
use App\Models\User;
use App\Repositories\Contracts\HabitLogRepositoryContract;
use App\Repositories\Contracts\HabitRepositoryContract;
use App\Services\Contracts\AchievementServiceContract;
use App\Services\Contracts\GamificationServiceContract;
use App\Services\Contracts\HabitLogServiceContract;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class HabitLogService
 */
class HabitLogService implements HabitLogServiceContract
{
    /**
     * HabitLogService constructor.
     */
    public function __construct(
        protected HabitLogRepositoryContract $habitLogRepository,
        protected HabitRepositoryContract $habitRepository,
        protected GamificationServiceContract $gamificationService,
        protected AchievementServiceContract $achievementService
    ) {}

    /**
     * Log habit completion and update stats/gamification.
     */
    public function logCompletion(
        User $user,
        Habit $habit,
        array $data
    ): array {
        $log = $this->habitLogRepository->updateOrCreate(
            [
                'habit_id' => $habit->id,
                'user_id' => $user->id,
                'completed_at' => $data['completed_at'] ?? today()->toDateString(),
            ],
            [
                'note' => $data['note'] ?? null,
                'count' => $data['count'] ?? 1,
            ]
        );

        // Update streak
        $streakInfo = $this->gamificationService->updateStreak($habit);

        // Update habit stats
        $habit->increment('total_completions');
        $this->habitRepository->updateStats($habit, [
            'last_completed_at' => now(),
        ]);

        // Award XP
        $xpInfo = $this->gamificationService->awardXpForHabit($user, $habit);

        // Check achievements
        $newAchievements = $this->achievementService->checkAchievements($user);

        return [
            'log' => $log,
            'gamification' => [
                'xp' => $xpInfo,
                'streak' => $streakInfo,
            ],
            'achievements' => [
                'newly_unlocked' => $newAchievements,
                'count' => $newAchievements->count(),
            ],
        ];
    }

    /**
     * Get logs for a habit.
     */
    public function getLogsForHabit(Habit $habit): Collection
    {
        return $this->habitLogRepository->getLogsForHabit($habit);
    }
}
