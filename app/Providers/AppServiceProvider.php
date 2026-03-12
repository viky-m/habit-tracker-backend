<?php

namespace App\Providers;

use App\Repositories\Contracts\AchievementRepositoryContract;
use App\Repositories\Contracts\HabitLogRepositoryContract;
use App\Repositories\Contracts\HabitReminderRepositoryContract;
use App\Repositories\Contracts\HabitRepositoryContract;
use App\Repositories\Contracts\HeroRepositoryContract;
use App\Repositories\Contracts\UserHeroRepositoryContract;
use App\Repositories\Contracts\UserRepositoryContract;
use App\Repositories\Eloquent\AchievementRepository;
use App\Repositories\Eloquent\HabitLogRepository;
use App\Repositories\Eloquent\HabitReminderRepository;
use App\Repositories\Eloquent\HabitRepository;
use App\Repositories\Eloquent\HeroRepository;
use App\Repositories\Eloquent\UserHeroRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Services\AchievementService;
use App\Services\AuthService;
use App\Services\Contracts\AchievementServiceContract;
use App\Services\Contracts\AuthServiceContract;
use App\Services\Contracts\GamificationServiceContract;
use App\Services\Contracts\HabitLogServiceContract;
use App\Services\Contracts\HabitReminderServiceContract;
use App\Services\Contracts\HabitServiceContract;
use App\Services\Contracts\HeroServiceContract;
use App\Services\Contracts\LevelUpServiceContract;
use App\Services\Contracts\UserHeroServiceContract;
use App\Services\Contracts\UserStatsServiceContract;
use App\Services\Contracts\XpCalculatorContract;
use App\Services\GamificationService;
use App\Services\HabitLogService;
use App\Services\HabitReminderService;
use App\Services\HabitService;
use App\Services\HeroService;
use App\Services\LevelUpService;
use App\Services\UserHeroService;
use App\Services\UserStatsService;
use App\Services\XpCalculator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repositories
        $this->app->bind(UserHeroRepositoryContract::class, UserHeroRepository::class);
        $this->app->bind(HeroRepositoryContract::class, HeroRepository::class);
        $this->app->bind(HabitRepositoryContract::class, HabitRepository::class);
        $this->app->bind(HabitLogRepositoryContract::class, HabitLogRepository::class);
        $this->app->bind(AchievementRepositoryContract::class, AchievementRepository::class);
        $this->app->bind(UserRepositoryContract::class, UserRepository::class);
        $this->app->bind(HabitReminderRepositoryContract::class, HabitReminderRepository::class);

        // Services
        $this->app->bind(AchievementServiceContract::class, AchievementService::class);
        $this->app->bind(AuthServiceContract::class, AuthService::class);
        $this->app->bind(GamificationServiceContract::class, GamificationService::class);
        $this->app->bind(HabitLogServiceContract::class, HabitLogService::class);
        $this->app->bind(HabitReminderServiceContract::class, HabitReminderService::class);
        $this->app->bind(HabitServiceContract::class, HabitService::class);
        $this->app->bind(HeroServiceContract::class, HeroService::class);
        $this->app->bind(LevelUpServiceContract::class, LevelUpService::class);
        $this->app->bind(UserHeroServiceContract::class, UserHeroService::class);
        $this->app->bind(UserStatsServiceContract::class, UserStatsService::class);
        $this->app->bind(XpCalculatorContract::class, XpCalculator::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            \URL::forceScheme('https');
        }
    }
}
