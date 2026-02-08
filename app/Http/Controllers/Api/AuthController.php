<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\SocialLoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * @group Authentication
 *
 * APIs for managing user authentication including registration, login, social auth, and profile.
 */
class AuthController extends Controller
{
    /**
     * Register a new user
     *
     * Register a new user with email and password. Returns user data and access token.
     *
     * @bodyParam name string required User's full name. Example: John Doe
     * @bodyParam email string required Valid email address. Example: john@example.com
     * @bodyParam password string required Minimum 8 characters. Example: password123
     * @bodyParam password_confirmation string required Must match password. Example: password123
     * @bodyParam locale string Locale: en or uk. Example: en
     *
     * @response 201 {
     *   "message": "User registered successfully",
     *   "user": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "avatar": null,
     *     "provider": "email",
     *     "locale": "en",
     *     "created_at": "2025-10-29T10:00:00.000000Z"
     *   },
     *   "token": "1|abc123xyz456..."
     * }
     * @response 422 {
     *   "message": "Validation failed",
     *   "errors": {
     *     "email": ["The email has already been taken."]
     *   }
     * }
     */
    public function register(RegisterRequest $request, GamificationService $gamification): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'provider' => 'email',
            'locale' => $request->locale ?? 'en',
        ]);

        // ⚡ ONBOARDING: Create first hero automatically
        $firstHero = $gamification->createFirstHero($user);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => new UserResource($user),
            'token' => $token,
            'first_hero' => [
                'id' => $firstHero->id,
                'name' => $firstHero->hero->name ?? 'Warrior',
                'level' => $firstHero->level,
            ],
        ], 201);
    }

    /**
     * Login with email and password
     *
     * Authenticate user and get access token.
     *
     * @bodyParam email string required User's email. Example: john@example.com
     * @bodyParam password string required User's password. Example: password123
     *
     * @response 200 {
     *   "message": "Login successful",
     *   "user": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com"
     *   },
     *   "token": "1|abc123xyz456..."
     * }
     * @response 401 {
     *   "message": "Invalid credentials"
     * }
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (! Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }

    /**
     * Login with social provider
     *
     * Authenticate user with Apple ID or Google ID. Creates new user if doesn't exist.
     *
     * @bodyParam provider string required Provider: apple or google. Example: apple
     * @bodyParam provider_id string required Unique ID from provider. Example: 001234.abc123def456.0123
     * @bodyParam email string Email from provider (optional for Apple). Example: john@example.com
     * @bodyParam name string User's name from provider. Example: John Doe
     * @bodyParam avatar string URL to user's avatar. Example: https://example.com/avatar.jpg
     * @bodyParam locale string Locale preference. Example: en
     *
     * @response 200 {
     *   "message": "Login successful",
     *   "user": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "provider": "apple"
     *   },
     *   "token": "1|abc123xyz456..."
     * }
     */
    public function socialLogin(SocialLoginRequest $request, GamificationService $gamification): JsonResponse
    {
        $providerIdField = $request->provider.'_id';

        // Try to find user by provider ID first
        $user = User::where($providerIdField, $request->provider_id)->first();

        $isNewUser = false;
        $firstHero = null;

        if (! $user) {
            // If not found by provider ID, try to find by email (link existing account)
            if ($request->email) {
                $user = User::where('email', $request->email)->first();

                if ($user) {
                    // Link this social provider to existing user
                    $user->update([
                        $providerIdField => $request->provider_id,
                        'avatar' => $request->avatar ?? $user->avatar,
                    ]);
                }
            }

            // If still no user, create a new one
            if (! $user) {
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    $providerIdField => $request->provider_id,
                    'provider' => $request->provider,
                    'avatar' => $request->avatar,
                    'locale' => $request->locale ?? 'en',
                ]);
                $isNewUser = true;

                // ⚡ ONBOARDING: Create first hero for new user
                $firstHero = $gamification->createFirstHero($user);
            }
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        $response = [
            'message' => $isNewUser ? 'User created successfully' : 'Login successful',
            'user' => new UserResource($user),
            'token' => $token,
            'is_new_user' => $isNewUser,
        ];

        if ($firstHero) {
            $response['first_hero'] = [
                'id' => $firstHero->id,
                'name' => $firstHero->hero->name ?? 'Warrior',
                'level' => $firstHero->level,
            ];
        }

        return response()->json($response, $isNewUser ? 201 : 200);
    }

    /**
     * Logout user
     *
     * Invalidate the current access token.
     *
     * @authenticated
     *
     * @response 200 {
     *   "message": "Logout successful"
     * }
     */
    public function logout(): JsonResponse
    {
        $token = Auth::user()->currentAccessToken();

        // Check if it's not a transient token (used in tests with Sanctum::actingAs)
        if ($token && ! ($token instanceof \Laravel\Sanctum\TransientToken)) {
            $token->delete();
        }

        return response()->json([
            'message' => 'Logout successful',
        ]);
    }

    /**
     * Get current user profile
     *
     * Returns authenticated user's profile information.
     *
     * @authenticated
     *
     * @response 200 {
     *   "data": {
     *     "id": 1,
     *     "name": "John Doe",
     *     "email": "john@example.com",
     *     "avatar": null,
     *     "provider": "email",
     *     "locale": "en",
     *     "created_at": "2025-10-29T10:00:00.000000Z"
     *   }
     * }
     */
    public function me(): JsonResponse
    {
        return response()->json([
            'user' => new UserResource(Auth::user()),
        ]);
    }
}
