<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserHero;

class UserHeroPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UserHero $userHero): bool
    {
        return $userHero->user_id === $user->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UserHero $userHero): bool
    {
        return $userHero->user_id === $user->id;
    }
}
