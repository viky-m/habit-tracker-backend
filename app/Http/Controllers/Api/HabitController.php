<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Habit\StoreHabitRequest;
use App\Http\Requests\Habit\UpdateHabitRequest;
use App\Http\Resources\HabitResource;
use App\Http\Resources\HabitStatsResource;
use App\Http\Resources\MessageResource;
use App\Models\Habit;
use App\Services\Contracts\HabitServiceContract;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

/**
 * @group Habits
 *
 * Manage user habits with CRUD operations
 */
class HabitController extends Controller
{
    /**
     * HabitController constructor.
     */
    public function __construct(
        protected HabitServiceContract $habitService
    ) {}

    /**
     * Get all user's habits
     *
     * Returns list of all habits for authenticated user
     *
     * @authenticated
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "title": "Morning exercise",
     *       "icon": "💪",
     *       "streak": 5,
     *       "is_completed_today": false
     *     }
     *   ]
     * }
     */
    public function index(): AnonymousResourceCollection
    {
        $filters = request()->only(['is_active']);
        $habits = $this->habitService->getHabitsForUser(Auth::user(), $filters);

        return HabitResource::collection($habits);
    }

    /**
     * Create a new habit
     *
     * @authenticated
     *
     * @bodyParam title string required Habit title. Example: Morning exercise
     * @bodyParam description string Habit description. Example: Do 30 pushups
     * @bodyParam icon string Emoji icon. Example: 💪
     * @bodyParam color string Hex color. Example: #6366f1
     * @bodyParam frequency string Frequency: daily, weekly, monthly. Example: daily
     * @bodyParam frequency_days array Days for custom frequency. Example: [1,3,5]
     * @bodyParam target_count integer Target completions per day. Example: 1
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "title": "Morning exercise",
     *     "streak": 0
     *   }
     * }
     */
    public function store(StoreHabitRequest $request): HabitResource
    {
        $habit = $this->habitService->createHabit(Auth::user(), $request->validated());

        return new HabitResource($habit);
    }

    /**
     * Get habit details
     *
     * @authenticated
     *
     * @urlParam habit integer required Habit ID. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "title": "Morning exercise",
     *     "streak": 5
     *   }
     * }
     */
    public function show(Habit $habit): HabitResource
    {
        return new HabitResource($habit);
    }

    /**
     * Update habit
     *
     * @authenticated
     *
     * @urlParam habit integer required Habit ID. Example: 1
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "title": "Updated title"
     *   }
     * }
     */
    public function update(UpdateHabitRequest $request, Habit $habit): HabitResource
    {
        $this->habitService->updateHabit($habit, $request->validated());

        return new HabitResource($habit);
    }

    /**
     * Delete habit
     *
     * @authenticated
     *
     * @urlParam habit integer required Habit ID. Example: 1
     *
     * @response 200 {
     *   "message": "Habit deleted successfully"
     * }
     */
    public function destroy(Habit $habit): MessageResource
    {
        $this->habitService->deleteHabit($habit);

        return new MessageResource('Habit deleted successfully');
    }

    /**
     * Get habit statistics
     *
     * @authenticated
     *
     * @urlParam habit integer required Habit ID. Example: 1
     *
     * @response 200 {
     *   "total_completions": 5,
     *   "current_streak": 2,
     *   "best_streak": 5,
     *   "completion_rate_30_days": 16.6,
     *   "last_completed_at": "2023-10-25T10:00:00.000000Z",
     *   "is_completed_today": true
     * }
     */
    public function getStats(Habit $habit): HabitStatsResource
    {
        $stats = $this->habitService->getHabitStats($habit);

        return new HabitStatsResource($stats);
    }
}
