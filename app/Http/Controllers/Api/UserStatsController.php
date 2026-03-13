<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserStatsResource;
use App\Services\Contracts\UserStatsServiceContract;
use Illuminate\Support\Facades\Auth;

/**
 * @group User Statistics
 *
 * User progress and statistics endpoints
 */
class UserStatsController extends Controller
{
    /**
     * UserStatsController constructor.
     */
    public function __construct(
        protected UserStatsServiceContract $userStatsService
    ) {}

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
    public function index(): UserStatsResource
    {
        $stats = $this->userStatsService->getOverallStats(Auth::user());

        return new UserStatsResource($stats);
    }
}
