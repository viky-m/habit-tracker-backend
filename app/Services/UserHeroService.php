<?php

namespace App\Services;

use App\Models\Hero;
use App\Models\User;
use App\Models\UserHero;
use App\Repositories\Contracts\UserHeroRepositoryContract;
use App\Services\Contracts\UserHeroServiceContract;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class UserHeroService
 */
class UserHeroService implements UserHeroServiceContract
{
    /**
     * UserHeroService constructor.
     */
    public function __construct(
        protected UserHeroRepositoryContract $userHeroRepository
    ) {}

    /**
     * Get all heroes for the given user.
     */
    public function getUserHeroes(User $user): Collection
    {
        return $this->userHeroRepository->getAllForUser($user);
    }

    /**
     * Get the active hero for the given user.
     */
    public function getActiveHero(User $user): ?UserHero
    {
        return $this->userHeroRepository->getActiveHero($user);
    }

    /**
     * Unlock a hero for the user and return it.
     */
    public function unlockHero(User $user, Hero $hero): UserHero
    {
        $userHero = $this->userHeroRepository->unlockHero($user, $hero);
        $userHero->load('hero');

        return $userHero;
    }

    /**
     * Switch the active hero for the user.
     */
    public function activateHero(User $user, UserHero $userHero): UserHero
    {
        $this->userHeroRepository->deactivateAllForUser($user);
        $this->userHeroRepository->activateHero($userHero);

        $userHero->load('hero');

        return $userHero;
    }
}
