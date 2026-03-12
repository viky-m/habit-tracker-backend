<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\SocialLoginRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Http\Resources\Auth\MeResource;
use App\Http\Resources\MessageResource;
use App\Services\Contracts\AuthServiceContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

/**
 * @group Authentication
 *
 * APIs for managing user authentication including registration, login, social auth, and profile.
 */
class AuthController extends Controller
{
    /**
     * AuthController constructor.
     */
    public function __construct(
        protected AuthServiceContract $authService
    ) {}

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
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());
        $result['message'] = 'User registered successfully';

        return (new LoginResource($result))
            ->response()
            ->setStatusCode(201);
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
        $result = $this->authService->login($request->only('email', 'password'));

        if (! $result) {
            return (new MessageResource('Invalid credentials'))
                ->response()
                ->setStatusCode(401);
        }

        $result['message'] = 'Login successful';
        $result['token_type'] = 'Bearer';

        return (new LoginResource($result))
            ->response()
            ->setStatusCode(200);
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
    public function socialLogin(SocialLoginRequest $request): JsonResponse
    {
        $result = $this->authService->socialLogin($request->validated());

        $isNewUser = $result['is_new_user'];
        $result['message'] = $isNewUser ? 'User created successfully' : 'Login successful';

        return (new LoginResource($result))
            ->response()
            ->setStatusCode($isNewUser ? 201 : 200);
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
        $this->authService->logout(Auth::user());

        return (new MessageResource('Logout successful'))
            ->response()
            ->setStatusCode(200);
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
        return (new MeResource(Auth::user()))
            ->response()
            ->setStatusCode(200);
    }
}
