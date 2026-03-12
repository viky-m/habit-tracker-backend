<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryContract;
use App\Services\Contracts\AuthServiceContract;
use App\Services\Contracts\GamificationServiceContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Class AuthService
 */
class AuthService implements AuthServiceContract
{
    /**
     * AuthService constructor.
     */
    public function __construct(
        protected UserRepositoryContract $userRepository,
        protected GamificationServiceContract $gamificationService
    ) {}

    /**
     * Register a new user.
     */
    public function register(array $data): array
    {
        $user = $this->userRepository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'provider' => 'email',
            'locale' => $data['locale'] ?? 'en',
        ]);

        $firstHero = $this->gamificationService->createFirstHero($user);
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'first_hero' => $firstHero,
        ];
    }

    /**
     * Login user.
     */
    public function login(array $credentials): ?array
    {
        if (! Auth::attempt($credentials)) {
            return null;
        }

        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Handle social login.
     */
    public function socialLogin(array $data): array
    {
        $provider = $data['provider'];
        $providerId = $data['provider_id'];
        $providerIdField = $provider.'_id';

        $user = $this->userRepository->findByProvider($provider, $providerId);
        $isNewUser = false;
        $firstHero = null;

        if (! $user) {
            if (isset($data['email'])) {
                $user = $this->userRepository->findByEmail($data['email']);

                if ($user) {
                    $this->userRepository->update($user, [
                        $providerIdField => $providerId,
                        'avatar' => $data['avatar'] ?? $user->avatar,
                    ]);
                }
            }

            if (! $user) {
                $user = $this->userRepository->create([
                    'name' => $data['name'],
                    'email' => $data['email'] ?? null,
                    $providerIdField => $providerId,
                    'provider' => $provider,
                    'avatar' => $data['avatar'] ?? null,
                    'locale' => $data['locale'] ?? 'en',
                ]);
                $isNewUser = true;
                $firstHero = $this->gamificationService->createFirstHero($user);
            }
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
            'is_new_user' => $isNewUser,
            'first_hero' => $firstHero,
        ];
    }

    /**
     * Logout user.
     */
    public function logout(User $user): void
    {
        $token = $user->currentAccessToken();

        if ($token && ! ($token instanceof \Laravel\Sanctum\TransientToken)) {
            $token->delete();
        }
    }
}
