<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HabitLog\LogHabitRequest;
use App\Http\Resources\HabitLogCompletionResource;
use App\Http\Resources\HabitLogResource;
use App\Http\Resources\HabitStatsResource;
use App\Models\Habit;
use App\Services\Contracts\HabitLogServiceContract;
use App\Services\Contracts\HabitServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

/**
 * @group Habit Logs
 *
 * APIs for logging habit completions and tracking progress.
 */
class HabitLogController extends Controller
{
    /**
     * HabitLogController constructor.
     */
    public function __construct(
        protected HabitLogServiceContract $habitLogService,
        protected HabitServiceContract $habitService
    ) {}

    /**
     * Log habit completion
     *
     * Mark habit as completed and update gamification stats.
     *
     * @authenticated
     *
     * @urlParam habit integer required The ID of the habit. Example: 1
     *
     * @bodyParam completed_at date User completion date. Example: 2025-10-29
     * @bodyParam note string Note about completion. Example: Feeling energized!
     * @bodyParam count integer Number of completions (for counter-based habits). Example: 1
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "habit_id": 1,
     *     "completed_at": "2025-10-29",
     *     "note": "Feeling energized!",
     *     "count": 1,
     *     "created_at": "2025-10-29T10:00:00.000000Z"
     *   },
     *   "gamification": {
     *     "xp": {"gained": 10, "total": 150, "level_up": false},
     *     "streak": {"current": 5, "best": 10}
     *   },
     *   "achievements": {
     *     "newly_unlocked": [],
     *     "count": 0
     *   }
     * }
     */
    public function store(
        LogHabitRequest $request,
        Habit $habit
    ): JsonResponse {
        $result = $this->habitLogService->logCompletion(
            Auth::user(),
            $habit,
            $request->validated()
        );

        return (new HabitLogCompletionResource($result))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Get habit logs
     *
     * Returns a history of habit executions.
     *
     * @authenticated
     *
     * @urlParam habit integer required The ID of the habit. Example: 1
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "habit_id": 1,
     *       "completed_at": "2025-10-29",
     *       "note": "Feeling energized!",
     *       "count": 1,
     *       "created_at": "2025-10-29T10:00:00.000000Z"
     *     }
     *   ]
     * }
     */
    public function index(Habit $habit): AnonymousResourceCollection
    {
        $logs = $this->habitLogService->getLogsForHabit($habit);

        return HabitLogResource::collection($logs);
    }

    /**
     * Get habit statistics
     *
     * Returns execution statistics for a specific habit.
     *
     * @authenticated
     *
     * @urlParam habit integer required The ID of the habit. Example: 1
     *
     * @response 200 {
     *   "total_completions": 10,
     *   "current_streak": 3,
     *   "best_streak": 7,
     *   "completion_rate_30_days": 85.5,
     *   "last_completed_at": "2025-10-29T10:00:00.000000Z",
     *   "is_completed_today": true
     * }
     */
    public function stats(Habit $habit): HabitStatsResource
    {
        $stats = $this->habitService->getHabitStats($habit);

        return new HabitStatsResource($stats);
    }
}
