<?php

namespace App\Providers;

use App\Services\AchievementService;
use App\Services\Contracts\AchievementServiceContract;
use App\Services\Contracts\GamificationServiceContract;
use App\Services\Contracts\HabitReminderServiceContract;
use App\Services\Contracts\LevelUpServiceContract;
use App\Services\Contracts\XpCalculatorContract;
use App\Services\GamificationService;
use App\Services\HabitReminderService;
use App\Services\LevelUpService;
use App\Services\XpCalculator;
use Illuminate\Support\ServiceProvider;

/**
 * Gamification Service Provider
 * Binds interfaces to implementations
 * Following Dependency Inversion Principle
 */
class GamificationServiceProvider extends ServiceProvider
{
    /**
     * Register gamification services
     */
    public function register(): void
    {
        // Bind XP Calculator
        $this->app->singleton(XpCalculatorContract::class, XpCalculator::class);

        // Bind Level Up Service
        $this->app->singleton(LevelUpServiceContract::class, LevelUpService::class);

        // Bind Gamification Service
        $this->app->singleton(GamificationServiceContract::class, GamificationService::class);

        // Bind Reminder Service
        $this->app->singleton(HabitReminderServiceContract::class, HabitReminderService::class);

        // Bind Achievement Service
        $this->app->singleton(AchievementServiceContract::class, AchievementService::class);
    }

    /**
     * Bootstrap services
     */
    public function boot(): void
    {
        //
    }
}
