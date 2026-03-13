<?php

namespace App\Repositories\Contracts;

use App\Models\Hero;
use App\Models\User;
use App\Models\UserHero;
use Illuminate\Database\Eloquent\Collection;

/**
 * Contract UserHeroRepositoryContract
 */
interface UserHeroRepositoryContract
{
    /**
     * Get all heroes for a specific user.
     */
    public function getAllForUser(User $user): Collection;

    /**
     * Get the currently active hero for a user.
     */
    public function getActiveHero(User $user): ?UserHero;

    /**
     * Unlock a hero for a user.
     */
    public function unlockHero(User $user, Hero $hero): UserHero;

    /**
     * Deactivate all heroes for a user.
     */
    public function deactivateAllForUser(User $user): bool;

    /**
     * Activate a specific user hero.
     */
    public function activateHero(UserHero $userHero): bool;

    /**
     * Create a new user hero record.
     */
    public function create(array $data): UserHero;

    /**
     * Update a user hero record.
     */
    public function update(UserHero $userHero, array $data): bool;
}
