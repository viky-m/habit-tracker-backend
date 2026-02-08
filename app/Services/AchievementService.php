<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\User;
use App\Services\Contracts\AchievementServiceContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Achievement Service
 * Manages user achievements and unlocking logic
 */
class AchievementService implements AchievementServiceContract
{
    /**
     * Check and unlock achievements for user based on their progress
     */
    public function checkAchievements(User $user): Collection
    {
        $unlockedAchievements = collect();

        $achievements = Achievement::whereNotIn('id', function ($query) use ($user) {
            $query->select('achievement_id')
                ->from('user_achievements')
                ->where('user_id', $user->id);
        })->get();

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

    /**
     * Check specific requirement
     */
    private function checkRequirement(User $user, string $key, mixed $value): bool
    {
        return match ($key) {
            'total_habits' => $user->habits()->count() >= $value,
            'total_completions' => $user->habitLogs()->count() >= $value,
            'current_streak' => $user->habits()->max('streak') >= $value,
            'hero_level' => $user->activeHero?->level >= $value,
            'days_registered' => $user->created_at->diffInDays(now()) >= $value,
            default => false,
        };
    }

    /**
     * Unlock specific achievement for user
     */
    public function unlockAchievement(User $user, Achievement $achievement, array $metadata = []): void
    {
        DB::table('user_achievements')->insertOrIgnore([
            'user_id' => $user->id,
            'achievement_id' => $achievement->id,
            'unlocked_at' => now(),
            'metadata' => json_encode($metadata),
            'is_notified' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Award bonus XP if achievement has reward
        if ($achievement->xp_reward > 0 && $user->activeHero) {
            $user->activeHero->increment('experience', $achievement->xp_reward);
        }

        Log::info('Achievement unlocked', [
            'user_id' => $user->id,
            'achievement_id' => $achievement->id,
            'xp_reward' => $achievement->xp_reward,
        ]);
    }

    /**
     * Get user's unlocked achievements
     */
    public function getUserAchievements(User $user): Collection
    {
        return $user->achievements()
            ->withPivot(['unlocked_at', 'metadata', 'is_notified'])
            ->orderBy('user_achievements.unlocked_at', 'desc')
            ->get();
    }

    /**
     * Get all available achievements
     */
    public function getAllAchievements(): Collection
    {
        return Achievement::where('is_secret', false)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get();
    }
}

