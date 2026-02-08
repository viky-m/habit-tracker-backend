<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Habit;
use App\Models\HabitReminder;
use App\Services\Contracts\HabitReminderServiceContract;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Habit Reminders
 *
 * Manage habit reminders and notifications
 */
class HabitReminderController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private HabitReminderServiceContract $reminderService
    ) {}

    /**
     * Get all user's reminders
     *
     * @authenticated
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "id": 1,
     *       "habit_id": 1,
     *       "time": "09:00:00",
     *       "days": [1,2,3,4,5],
     *       "timezone": "Europe/Kiev",
     *       "is_enabled": true,
     *       "notification_type": "push"
     *     }
     *   ]
     * }
     */
    public function index(): JsonResponse
    {
        $reminders = $this->reminderService->getUserReminders(auth()->user());

        return response()->json(['data' => $reminders]);
    }

    /**
     * Create reminder for habit
     *
     * @authenticated
     *
     * @bodyParam habit_id integer required Habit ID. Example: 1
     * @bodyParam time string required Time in HH:MM format. Example: 09:00
     * @bodyParam days array Days of week (1=Mon, 7=Sun). Example: [1,2,3,4,5]
     * @bodyParam timezone string User's timezone. Example: Europe/Kiev
     * @bodyParam notification_type string Type: push, email, both. Example: push
     * @bodyParam message string Custom reminder message. Example: Time to exercise!
     *
     * @response 201 {
     *   "data": {
     *     "id": 1,
     *     "habit_id": 1,
     *     "time": "09:00:00",
     *     "is_enabled": true
     *   }
     * }
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'habit_id' => ['required', 'exists:habits,id'],
            'time' => ['required', 'date_format:H:i'],
            'days' => ['sometimes', 'array'],
            'days.*' => ['integer', 'min:1', 'max:7'],
            'timezone' => ['sometimes', 'string', 'timezone'],
            'notification_type' => ['sometimes', 'in:push,email,both'],
            'message' => ['sometimes', 'string', 'max:255'],
        ]);

        $habit = Habit::findOrFail($validated['habit_id']);
        $this->authorize('view', $habit);

        $reminder = $this->reminderService->createReminder(
            auth()->user(),
            $habit,
            $validated
        );

        return response()->json(['data' => $reminder], 201);
    }

    /**
     * Update reminder
     *
     * @authenticated
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "is_enabled": false
     *   }
     * }
     */
    public function update(Request $request, HabitReminder $reminder): JsonResponse
    {
        $this->authorize('update', $reminder);

        $validated = $request->validate([
            'time' => ['sometimes', 'date_format:H:i'],
            'days' => ['sometimes', 'array'],
            'days.*' => ['integer', 'min:1', 'max:7'],
            'timezone' => ['sometimes', 'string', 'timezone'],
            'is_enabled' => ['sometimes', 'boolean'],
            'notification_type' => ['sometimes', 'in:push,email,both'],
            'message' => ['sometimes', 'string', 'max:255'],
        ]);

        $reminder->update($validated);

        return response()->json(['data' => $reminder]);
    }

    /**
     * Delete reminder
     *
     * @authenticated
     *
     * @response 200 {
     *   "message": "Reminder deleted successfully"
     * }
     */
    public function destroy(HabitReminder $reminder): JsonResponse
    {
        $this->authorize('delete', $reminder);

        $reminder->delete();

        return response()->json(['message' => 'Reminder deleted successfully']);
    }
}
