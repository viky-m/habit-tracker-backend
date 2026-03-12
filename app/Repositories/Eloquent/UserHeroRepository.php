<?php

namespace App\Repositories\Eloquent;

use App\Models\Hero;
use App\Models\User;
use App\Models\UserHero;
use App\Repositories\Contracts\UserHeroRepositoryContract;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class UserHeroRepository
 */
class UserHeroRepository implements UserHeroRepositoryContract
{
    /**
     * {@inheritDoc}
     */
    public function getAllForUser(User $user): Collection
    {
        return $user->userHeroes()->with('hero')->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getActiveHero(User $user): ?UserHero
    {
        return $user->activeHero()->with('hero')->first();
    }

    /**
     * {@inheritDoc}
     */
    public function unlockHero(User $user, Hero $hero): UserHero
    {
        return $user->userHeroes()->firstOrCreate(
            ['hero_id' => $hero->id],
            [
                'level' => 1,
                'experience' => 0,
                'is_unlocked' => true,
                'stats' => $hero->stats,
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function deactivateAllForUser(User $user): bool
    {
        return $user->userHeroes()->update(['is_active' => false]) >= 0;
    }

    /**
     * {@inheritDoc}
     */
    public function activateHero(UserHero $userHero): bool
    {
        return $userHero->update([
            'is_active' => true,
            'last_active_at' => now(),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function create(array $data): UserHero
    {
        return UserHero::create($data);
    }

    /**
     * {@inheritDoc}
     */
    public function update(UserHero $userHero, array $data): bool
    {
        return $userHero->update($data);
    }
}
