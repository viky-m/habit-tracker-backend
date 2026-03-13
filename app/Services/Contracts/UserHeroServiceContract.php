<?php

namespace App\Services\Contracts;

use App\Models\Hero;
use App\Models\User;
use App\Models\UserHero;
use Illuminate\Database\Eloquent\Collection;

interface UserHeroServiceContract
{
    public function getUserHeroes(User $user): Collection;

    public function getActiveHero(User $user): ?UserHero;

    public function unlockHero(User $user, Hero $hero): UserHero;

    public function activateHero(User $user, UserHero $userHero): UserHero;
}
