<?php

namespace App\Services\Contracts;

use App\Models\User;

interface AuthServiceContract
{
    /**
     * Register a new user.
     */
    public function register(array $data): array;

    /**
     * Login user.
     */
    public function login(array $credentials): ?array;

    /**
     * Handle social login.
     */
    public function socialLogin(array $data): array;

    /**
     * Logout user.
     */
    public function logout(User $user): void;
}
