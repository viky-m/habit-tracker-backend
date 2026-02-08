<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HabitController;
use App\Http\Controllers\Api\HabitLogController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\HeroController;
use App\Http\Controllers\Api\UserHeroController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Health check
Route::get('/health', [HealthController::class, 'index']);

// Authentication routes (public)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/social-login', [AuthController::class, 'socialLogin']);
});

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    // User Statistics
    Route::get('/user/stats', [\App\Http\Controllers\Api\UserStatsController::class, 'index']);

    // Reminders
    Route::prefix('reminders')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\HabitReminderController::class, 'index']);
        Route::post('/', [\App\Http\Controllers\Api\HabitReminderController::class, 'store']);
        Route::put('/{reminder}', [\App\Http\Controllers\Api\HabitReminderController::class, 'update']);
        Route::delete('/{reminder}', [\App\Http\Controllers\Api\HabitReminderController::class, 'destroy']);
    });

    // Achievements
    Route::prefix('achievements')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\AchievementController::class, 'index']);
        Route::get('/user', [\App\Http\Controllers\Api\AchievementController::class, 'userAchievements']);
        Route::post('/check', [\App\Http\Controllers\Api\AchievementController::class, 'checkNew']);
    });

    // Habits CRUD
    Route::apiResource('habits', HabitController::class);

    // Habit Logs
    Route::prefix('habits/{habit}')->group(function () {
        Route::post('/log', [HabitLogController::class, 'store']);
        Route::get('/logs', [HabitLogController::class, 'index']);
        Route::get('/stats', [HabitLogController::class, 'stats']);
    });

    // Heroes (public list)
    Route::get('/heroes', [HeroController::class, 'index']);
    Route::get('/heroes/{hero}', [HeroController::class, 'show']);

    // User Heroes
    Route::prefix('user/heroes')->group(function () {
        Route::get('/', [UserHeroController::class, 'index']);
        Route::get('/active', [UserHeroController::class, 'active']);
        Route::post('/{hero}/unlock', [UserHeroController::class, 'unlock']);
        Route::post('/{userHero}/activate', [UserHeroController::class, 'activate']);
    });
});
