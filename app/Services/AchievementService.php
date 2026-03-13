<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\User;
use App\Repositories\Contracts\AchievementRepositoryContract;
use App\Repositories\Contracts\HabitLogRepositoryContract;
use App\Repositories\Contracts\HabitRepositoryContract;
use App\Repositories\Contracts\UserHeroRepositoryContract;
use App\Services\Contracts\AchievementServiceContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Achievement Service
 * Manages user achievements and unlocking logic
 */
class AchievementService implements AchievementServiceContract
{
    /**
     * AchievementService constructor.
     */
    public function __construct(
        protected AchievementRepositoryContract $achievementRepository,
        protected UserHeroRepositoryContract $userHeroRepository,
        protected HabitRepositoryContract $habitRepository,
        protected HabitLogRepositoryContract $habitLogRepository
    ) {}

    /**
     * {@inheritDoc}
     */
    public function checkAchievements(User $user): Collection
    {
        $unlockedAchievements = collect();

        $achievements = $this->achievementRepository->getLockedForUser($user);

        foreach ($achievements as $achievement) {
            if ($this->meetsRequirements($user, $achievement)) {
                $this->unlockAchievement($user, $achievement);
                $unlockedAchievements->push($achievement);
            }
        }

        return $unlockedAchievements;
    }

    /**
     * Check if user meets achievement requirements
     */
    private function meetsRequirements(User $user, Achievement $achievement): bool
    {
        $requirements = $achievement->requirements ?? [];

        foreach ($requirements as $key => $value) {
            if (! $this->checkRequirement($user, $key, $value)) {
                return false;
            }
        }

        return true;
    }

    private function checkRequirement(User $user, string $key, mixed $value): bool
    {
        $activeHero = $this->userHeroRepository->getActiveHero($user);

        return match ($key) {
            'total_habits' => $this->habitRepository->countForUser($user) >= $value,
            'total_completions' => $this->habitLogRepository->countForUser($user) >= $value,
            'current_streak' => $this->habitRepository->getMaxStreakForUser($user) >= $value,
            'hero_level' => ($activeHero?->level ?? 0) >= $value,
            'days_registered' => $user->created_at->diffInDays(now()) >= $value,
            default => false,
        };
    }

    /**
     * {@inheritDoc}
     */
    public function unlockAchievement(User $user, Achievement $achievement, array $metadata = []): void
    {
        $unlocked = $this->achievementRepository->unlockForUser($user, $achievement, $metadata);

        if ($unlocked) {
            // Award bonus XP if achievement has reward
            if ($achievement->xp_reward > 0 && $user->activeHero) {
                $this->userHeroRepository->update($user->activeHero, [
                    'experience' => $user->activeHero->experience + $achievement->xp_reward,
                ]);
            }

            Log::info('Achievement unlocked', [
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
                'xp_reward' => $achievement->xp_reward,
            ]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getUserAchievements(User $user): Collection
    {
        return $this->achievementRepository->getUnlockedForUser($user);
    }

    /**
     * {@inheritDoc}
     */
    public function getAllAchievements(): Collection
    {
        return $this->achievementRepository->getAll();
    }
}
