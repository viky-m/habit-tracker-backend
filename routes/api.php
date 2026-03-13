<?php

use App\Http\Controllers\Api\AchievementController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HabitController;
use App\Http\Controllers\Api\HabitLogController;
use App\Http\Controllers\Api\HabitReminderController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\HeroController;
use App\Http\Controllers\Api\UserHeroController;
use App\Http\Controllers\Api\UserStatsController;
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
    Route::get('/user/stats', [UserStatsController::class, 'index']);

    // Reminders
    Route::prefix('reminders')->group(function () {
        Route::get('/', [HabitReminderController::class, 'index']);
        Route::post('/', [HabitReminderController::class, 'store']);
        Route::put('/{reminder}', [HabitReminderController::class, 'update'])
            ->middleware('can:update,reminder');
        Route::delete('/{reminder}', [HabitReminderController::class, 'destroy'])
            ->middleware('can:delete,reminder');
    });

    // Achievements
    Route::prefix('achievements')->group(function () {
        Route::get('/', [AchievementController::class, 'index']);
        Route::get('/user', [AchievementController::class, 'userAchievements']);
        Route::post('/check', [AchievementController::class, 'checkNew']);
    });

    // Habits CRUD
    Route::prefix('habits')->group(function () {
        Route::get('/', [HabitController::class, 'index']);
        Route::post('/', [HabitController::class, 'store']);
        Route::get('/{habit}', [HabitController::class, 'show'])
            ->middleware('can:view,habit');
        Route::put('/{habit}', [HabitController::class, 'update'])
            ->middleware('can:update,habit');
        Route::delete('/{habit}', [HabitController::class, 'destroy'])
            ->middleware('can:delete,habit');
    });

    // Habit Logs
    Route::prefix('habits/{habit}')->group(function () {
        Route::post('/log', [HabitLogController::class, 'store'])
            ->middleware('can:view,habit');
        Route::get('/logs', [HabitLogController::class, 'index'])
            ->middleware('can:view,habit');
        Route::get('/stats', [HabitController::class, 'getStats'])
            ->middleware('can:view,habit');
    });

    // Heroes (public list)
    Route::get('/heroes', [HeroController::class, 'index']);
    Route::get('/heroes/{hero}', [HeroController::class, 'show']);

    // User Heroes
    Route::prefix('user/heroes')->group(function () {
        Route::get('/', [UserHeroController::class, 'index']);
        Route::get('/active', [UserHeroController::class, 'active']);
        Route::post('/{hero}/unlock', [UserHeroController::class, 'unlock']);
        Route::post('/{userHero}/activate', [UserHeroController::class, 'activate'])
            ->middleware('can:update,userHero');
    });
});
