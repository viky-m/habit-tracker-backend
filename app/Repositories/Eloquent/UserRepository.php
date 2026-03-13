<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryContract;

/**
 * Class UserRepository
 */
class UserRepository implements UserRepositoryContract
{
    /**
     * {@inheritDoc}
     */
    public function create(array $data): User
    {
        return User::create($data);
    }

    /**
     * {@inheritDoc}
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * {@inheritDoc}
     */
    public function findByProvider(string $provider, string $providerId): ?User
    {
        $field = $provider.'_id';

        return User::where($field, $providerId)->first();
    }

    /**
     * {@inheritDoc}
     */
    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }
}
