<?php

namespace App\Repositories\Contracts;

use App\Models\User;

/**
 * Contract UserRepositoryContract
 */
interface UserRepositoryContract
{
    /**
     * Create a new user.
     */
    public function create(array $data): User;

    /**
     * Find a user by email.
     */
    public function findByEmail(string $email): ?User;

    /**
     * Find a user by provider ID.
     */
    public function findByProvider(string $provider, string $providerId): ?User;

    /**
     * Update user information.
     */
    public function update(User $user, array $data): bool;
}
