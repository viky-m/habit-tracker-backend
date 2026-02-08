<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @group User Statistics
 *
 * User progress and statistics endpoints
 */
class UserStatsController extends Controller
{
    /**
     * Get user statistics
     *
     * Overall user progress, habits completion rate, hero stats, and achievements
     *
     * @authenticated
     *
     * @response 200 {
     *   "habits": {
     *     "total": 5,
     *     "active": 4,
     *     "completed_today": 2,
     *     "completion_rate_7_days": 85.7,
     *     "completion_rate_30_days": 78.3,
     *     "total_completions": 156
     *   },
     *   "streaks": {
     *     "longest_current": 12,
     *     "longest_ever": 24,
     *     "total_streak_days": 156
     *   },
     *   "hero": {
     *     "name": "Warrior",
     *     "level": 5,
     *     "experience": 523,
     *     "next_level_xp": 620,
     *     "progress_percent": 84.3
     *   },
     *   "achievements": {
     *     "total_unlocked": 3,
     *     "recent": []
     *   }
     * }
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();

        // Habits statistics
        $habitsStats = [
            'total' => $user->habits()->count(),
            'active' => $user->habits()->where('is_active', true)->count(),
            'completed_today' => $user->habits()->whereHas('logs', function ($query) {
                $query->whereDate('completed_at', today());
            })->count(),
            'completion_rate_7_days' => $this->getCompletionRate($user, 7),
            'completion_rate_30_days' => $this->getCompletionRate($user, 30),
            'total_completions' => $user->habitLogs()->count(),
        ];

        // Streaks statistics
        $streaksStats = [
            'longest_current' => $user->habits()->max('streak') ?? 0,
            'longest_ever' => $user->habits()->max('best_streak') ?? 0,
            'total_streak_days' => $user->habits()->sum('streak'),
        ];

        // Active hero statistics
        $activeHero = $user->activeHero;
        $heroStats = null;

        if ($activeHero) {
            $nextLevelXp = $this->getXpForLevel($activeHero->level + 1);
            $progress = ($activeHero->experience / $nextLevelXp) * 100;

            $heroStats = [
                'name' => $activeHero->hero->name ?? 'Unknown',
                'level' => $activeHero->level,
                'experience' => $activeHero->experience,
                'next_level_xp' => $nextLevelXp,
                'progress_percent' => round($progress, 1),
            ];
        }

        // Achievements (placeholder for future implementation)
        $achievementsStats = [
            'total_unlocked' => 0,
            'recent' => [],
        ];

        return response()->json([
            'habits' => $habitsStats,
            'streaks' => $streaksStats,
            'hero' => $heroStats,
            'achievements' => $achievementsStats,
        ]);
    }

    /**
     * Calculate completion rate for last N days
     */
    private function getCompletionRate($user, int $days): float
    {
        $activeHabits = $user->activeHabits()->count();

        if ($activeHabits === 0) {
            return 0.0;
        }

        $expectedCompletions = $activeHabits * $days;
        $actualCompletions = $user->habitLogs()
            ->where('completed_at', '>=', now()->subDays($days))
            ->count();

        return round(($actualCompletions / $expectedCompletions) * 100, 1);
    }

    /**
     * Calculate XP required for level
     */
    private function getXpForLevel(int $level): int
    {
        return 100 * $level + (int) (pow($level, 1.5) * 20);
    }
}

