<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Contracts\AchievementServiceContract;
use Illuminate\Http\JsonResponse;

/**
 * @group Achievements
 *
 * User achievements and unlocking system
 */
class AchievementController extends Controller
{
    public function __construct(
        private AchievementServiceContract $achievementService
    ) {}

    /**
     * Get all available achievements
     *
     * @authenticated
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "key": "first_habit",
     *       "title": "First Step",
     *       "description": "Create your first habit",
     *       "icon": "🎯",
     *       "category": "habits",
     *       "rarity": "common",
     *       "xp_reward": 10
     *     }
     *   ]
     * }
     */
    public function index(): JsonResponse
    {
        $achievements = $this->achievementService->getAllAchievements();

        return response()->json(['data' => $achievements]);
    }

    /**
     * Get user's unlocked achievements
     *
     * @authenticated
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "First Step",
     *       "unlocked_at": "2025-10-29T10:00:00.000000Z",
     *       "xp_reward": 10
     *     }
     *   ]
     * }
     */
    public function userAchievements(): JsonResponse
    {
        $achievements = $this->achievementService->getUserAchievements(auth()->user());

        return response()->json(['data' => $achievements]);
    }

    /**
     * Check for new achievements
     *
     * Check if user has unlocked any new achievements based on current progress
     *
     * @authenticated
     *
     * @response 200 {
     *   "newly_unlocked": [
     *     {
     *       "id": 2,
     *       "title": "Week Warrior",
     *       "description": "Complete 7 day streak",
     *       "xp_reward": 50
     *     }
     *   ],
     *   "count": 1
     * }
     */
    public function checkNew(): JsonResponse
    {
        $newAchievements = $this->achievementService->checkAchievements(auth()->user());

        return response()->json([
            'newly_unlocked' => $newAchievements,
            'count' => $newAchievements->count(),
        ]);
    }
}
