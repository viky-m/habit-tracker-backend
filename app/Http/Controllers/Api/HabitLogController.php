<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\HabitLog\LogHabitRequest;
use App\Http\Resources\HabitLogResource;
use App\Models\Habit;
use App\Services\Contracts\AchievementServiceContract;
use App\Services\Contracts\GamificationServiceContract;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class HabitLogController extends Controller
{
    use AuthorizesRequests;
    /**
     * @OA\Post(
     *     path="/habits/{habit}/log",
     *     tags={"Habit Logs"},
     *     summary="Log habit completion",
     *     description="Mark habit as completed",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="habit", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="completed_at", type="string", format="date", example="2025-10-29"),
     *             @OA\Property(property="note", type="string", example="Felt great!"),
     *             @OA\Property(property="count", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(response=201, description="Habit completion logged")
     * )
     */
    public function store(
        LogHabitRequest $request,
        Habit $habit,
        GamificationServiceContract $gamification,
        AchievementServiceContract $achievements
    ): JsonResponse {
        $this->authorize('view', $habit);

        $log = $habit->logs()->updateOrCreate(
            [
                'habit_id' => $habit->id,
                'user_id' => auth()->id(),
                'completed_at' => $request->input('completed_at', today()),
            ],
            [
                'note' => $request->input('note'),
                'count' => $request->input('count', 1),
            ]
        );

        // 🔥 GAMIFICATION: Update streak BEFORE updating last_completed_at
        $streakInfo = $gamification->updateStreak($habit);

        // Update habit stats
        $habit->increment('total_completions');
        $habit->last_completed_at = now();
        $habit->save();

        // 🔥 GAMIFICATION: Award XP to active hero
        $xpInfo = $gamification->awardXpForHabit(auth()->user(), $habit);

        // 🏆 ACHIEVEMENTS: Check for new achievements
        $newAchievements = $achievements->checkAchievements(auth()->user());

        return response()->json([
            'data' => new HabitLogResource($log),
            'gamification' => [
                'xp' => $xpInfo,
                'streak' => $streakInfo,
            ],
            'achievements' => [
                'newly_unlocked' => $newAchievements->map(fn($a) => [
                    'id' => $a->id,
                    'title' => $a->title,
                    'icon' => $a->icon,
                    'xp_reward' => $a->xp_reward,
                ]),
                'count' => $newAchievements->count(),
            ],
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/habits/{habit}/logs",
     *     tags={"Habit Logs"},
     *     summary="Get habit logs",
     *     description="History of habit executions",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="habit", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="List of logs")
     * )
     */
    public function index(Habit $habit): AnonymousResourceCollection
    {
        $this->authorize('view', $habit);

        $logs = $habit->logs()->orderBy('completed_at', 'desc')->get();

        return HabitLogResource::collection($logs);
    }

    /**
     * @OA\Get(
     *     path="/habits/{habit}/stats",
     *     tags={"Habit Logs"},
     *     summary="Get habit statistics",
     *     description="Habit execution statistics",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="habit", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Statistics")
     * )
     */
    public function stats(Habit $habit): JsonResponse
    {
        $this->authorize('view', $habit);

        $stats = [
            'total_completions' => $habit->total_completions,
            'current_streak' => $habit->streak,
            'best_streak' => $habit->best_streak,
            'completion_rate_30_days' => $habit->getCompletionRate(30),
            'last_completed_at' => $habit->last_completed_at,
            'is_completed_today' => $habit->isCompletedToday(),
        ];

        return response()->json($stats);
    }
}
