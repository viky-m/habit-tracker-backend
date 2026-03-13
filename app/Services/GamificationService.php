<?php

namespace App\Services;

use App\Models\Habit;
use App\Models\Hero;
use App\Models\User;
use App\Models\UserHero;
use App\Repositories\Contracts\HeroRepositoryContract;
use App\Repositories\Contracts\UserHeroRepositoryContract;
use App\Services\Contracts\GamificationServiceContract;
use App\Services\Contracts\LevelUpServiceContract;
use App\Services\Contracts\XpCalculatorContract;
use Illuminate\Support\Facades\Log;

/**
 * Gamification Service
 * Following SOLID principles:
 * - Single Responsibility: Coordinates gamification features
 * - Open/Closed: Open for extension through interfaces
 * - Liskov Substitution: Can be replaced with any implementation of contract
 * - Interface Segregation: Uses specific service interfaces
 * - Dependency Inversion: Depends on abstractions not concretions
 */
class GamificationService implements GamificationServiceContract
{
    public function __construct(
        private XpCalculatorContract $xpCalculator,
        private LevelUpServiceContract $levelUpService,
        protected HeroRepositoryContract $heroRepository,
        protected UserHeroRepositoryContract $userHeroRepository
    ) {}

    /**
     * Award XP to user's active hero when habit is completed
     */
    public function awardXpForHabit(User $user, Habit $habit): array
    {
        $activeHero = $this->userHeroRepository->getActiveHero($user);

        if (! $activeHero) {
            Log::warning('User has no active hero', ['user_id' => $user->id]);

            return $this->buildNoHeroResponse();
        }

        // Calculate XP using XpCalculator
        $xp = $this->xpCalculator->calculate($habit);

        // Award XP
        $activeHero->experience += $xp;

        // Check for level up using LevelUpService
        $leveledUp = $this->levelUpService->processLevelUp($activeHero);

        $this->userHeroRepository->update($activeHero, [
            'experience' => $activeHero->experience,
            'level' => $activeHero->level,
        ]);

        return $this->buildXpResponse($xp, $activeHero, $leveledUp);
    }

    /**
     * Build response when user has no active hero
     */
    private function buildNoHeroResponse(): array
    {
        return [
            'xp_awarded' => 0,
            'level_up' => false,
            'message' => 'No active hero',
        ];
    }

    /**
     * Build XP award response
     */
    private function buildXpResponse(int $xp, UserHero $hero, bool $leveledUp): array
    {
        return [
            'xp_awarded' => $xp,
            'total_xp' => $hero->experience,
            'level' => $hero->level,
            'level_up' => $leveledUp,
            'next_level_xp' => $this->xpCalculator->getXpForLevel($hero->level + 1),
        ];
    }

    /**
     * Update habit streak based on completion
     */
    public function updateStreak(Habit $habit): array
    {
        $today = today();
        $lastCompleted = $habit->last_completed_at ? $habit->last_completed_at->startOfDay() : null;

        // First completion ever or streak is 0
        if (! $lastCompleted || $habit->streak === 0) {
            $habit->streak = 1;
            $habit->best_streak = max($habit->best_streak, 1);

            return [
                'streak' => 1,
                'streak_status' => 'started',
            ];
        }

        // Calculate days difference
        $daysDiff = $lastCompleted->diffInDays($today);

        if ($daysDiff === 0) {
            // Already completed today - no change
            return [
                'streak' => $habit->streak,
                'streak_status' => 'maintained',
            ];
        } elseif ($daysDiff === 1) {
            // Consecutive day - increment streak
            $habit->streak++;
            $habit->best_streak = max($habit->best_streak, $habit->streak);

            return [
                'streak' => $habit->streak,
                'streak_status' => 'increased',
                'is_milestone' => ($habit->streak % 7 === 0), // Weekly milestone
            ];
        } else {
            // Streak broken
            $previousStreak = $habit->streak;
            $habit->streak = 1;

            return [
                'streak' => 1,
                'streak_status' => 'broken',
                'previous_streak' => $previousStreak,
            ];
        }
    }

    /**
     * Create and unlock first hero for new user
     */
    public function createFirstHero(User $user): UserHero
    {
        $starterHero = $this->findOrCreateStarterHero();

        $userHero = $this->createUserHeroInstance($user, $starterHero);

        Log::info('First hero created for user', [
            'user_id' => $user->id,
            'hero_id' => $starterHero->id,
        ]);

        return $userHero;
    }

    /**
     * Find starter hero or create default one
     */
    private function findOrCreateStarterHero(): Hero
    {
        $starterHero = $this->heroRepository->findStarterHero();

        if ($starterHero) {
            return $starterHero;
        }

        return $this->createDefaultStarterHero();
    }

    /**
     * Create default starter hero if none exist
     */
    private function createDefaultStarterHero(): Hero
    {
        return $this->heroRepository->create([
            'name' => 'Warrior',
            'description' => 'A brave warrior starting their journey',
            'model_url' => '/models/warrior.glb',
            'thumbnail_url' => '/images/heroes/warrior.png',
            'rarity' => 'common',
            'unlock_level' => 0,
            'unlock_cost' => 0,
            'is_premium' => false,
            'stats' => [
                'strength' => 5,
                'endurance' => 5,
                'agility' => 5,
            ],
        ]);
    }

    /**
     * Create user's hero instance
     */
    private function createUserHeroInstance(User $user, Hero $hero): UserHero
    {
        return $this->userHeroRepository->create([
            'user_id' => $user->id,
            'hero_id' => $hero->id,
            'level' => 1,
            'experience' => 0,
            'is_unlocked' => true,
            'is_active' => true,
            'stats' => $hero->stats ?? [],
        ]);
    }
}
