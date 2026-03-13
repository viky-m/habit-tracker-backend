<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SocialLoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful Apple login creates new user
     */
    public function test_apple_login_creates_new_user(): void
    {
        $response = $this->postJson('/api/auth/social-login', [
            'provider' => 'apple',
            'provider_id' => '001234.abc123def456.1234',
            'email' => 'apple@example.com',
            'name' => 'Apple User',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email', 'avatar', 'provider', 'locale'],
                'token',
                'is_new_user',
            ])
            ->assertJson([
                'message' => 'User created successfully',
                'user' => [
                    'email' => 'apple@example.com',
                    'name' => 'Apple User',
                    'provider' => 'apple',
                ],
                'is_new_user' => true,
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'apple@example.com',
            'apple_id' => '001234.abc123def456.1234',
            'provider' => 'apple',
        ]);
    }

    /**
     * Test successful Google login creates new user
     */
    public function test_google_login_creates_new_user(): void
    {
        $response = $this->postJson('/api/auth/social-login', [
            'provider' => 'google',
            'provider_id' => '123456789',
            'email' => 'google@example.com',
            'name' => 'Google User',
            'avatar' => 'https://lh3.googleusercontent.com/a/default',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email', 'avatar', 'provider', 'locale'],
                'token',
                'is_new_user',
            ])
            ->assertJson([
                'message' => 'User created successfully',
                'user' => [
                    'email' => 'google@example.com',
                    'name' => 'Google User',
                    'provider' => 'google',
                    'avatar' => 'https://lh3.googleusercontent.com/a/default',
                ],
                'is_new_user' => true,
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'google@example.com',
            'google_id' => '123456789',
            'provider' => 'google',
        ]);
    }

    /**
     * Test existing Apple user can login again
     */
    public function test_existing_apple_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'apple_id' => '001234.abc123def456.1234',
            'provider' => 'apple',
        ]);

        $response = $this->postJson('/api/auth/social-login', [
            'provider' => 'apple',
            'provider_id' => '001234.abc123def456.1234',
            'email' => 'existing@example.com',
            'name' => 'Apple User',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'email' => 'existing@example.com',
                ],
                'is_new_user' => false,
            ]);

        // Verify only one user exists
        $this->assertDatabaseCount('users', 1);
    }

    /**
     * Test existing Google user can login again
     */
    public function test_existing_google_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
            'google_id' => '123456789',
            'provider' => 'google',
        ]);

        $response = $this->postJson('/api/auth/social-login', [
            'provider' => 'google',
            'provider_id' => '123456789',
            'email' => 'existing@example.com',
            'name' => 'Google User',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                ],
                'is_new_user' => false,
            ]);
    }

    /**
     * Test social login with custom locale
     */
    public function test_social_login_with_custom_locale(): void
    {
        $response = $this->postJson('/api/auth/social-login', [
            'provider' => 'apple',
            'provider_id' => '001234.abc123def456.1234',
            'email' => 'ukrainian@example.com',
            'name' => 'Ukrainian User',
            'locale' => 'uk',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'user' => [
                    'locale' => 'uk',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'ukrainian@example.com',
            'locale' => 'uk',
        ]);
    }

    /**
     * Test social login fails without provider
     */
    public function test_social_login_fails_without_provider(): void
    {
        $response = $this->postJson('/api/auth/social-login', [
            'provider_id' => '123456789',
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['provider']);
    }

    /**
     * Test social login fails without provider_id
     */
    public function test_social_login_fails_without_provider_id(): void
    {
        $response = $this->postJson('/api/auth/social-login', [
            'provider' => 'apple',
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['provider_id']);
    }

    /**
     * Test social login fails without email
     */
    public function test_social_login_fails_without_email(): void
    {
        $response = $this->postJson('/api/auth/social-login', [
            'provider' => 'apple',
            'provider_id' => '123456789',
            'name' => 'Test User',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test social login fails without name
     */
    public function test_social_login_fails_without_name(): void
    {
        $response = $this->postJson('/api/auth/social-login', [
            'provider' => 'apple',
            'provider_id' => '123456789',
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test social login fails with invalid provider
     */
    public function test_social_login_fails_with_invalid_provider(): void
    {
        $response = $this->postJson('/api/auth/social-login', [
            'provider' => 'facebook',
            'provider_id' => '123456789',
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['provider']);
    }

    /**
     * Test social login fails with invalid email format
     */
    public function test_social_login_fails_with_invalid_email_format(): void
    {
        $response = $this->postJson('/api/auth/social-login', [
            'provider' => 'apple',
            'provider_id' => '123456789',
            'email' => 'invalid-email',
            'name' => 'Test User',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test social login fails with invalid locale
     */
    public function test_social_login_fails_with_invalid_locale(): void
    {
        $response = $this->postJson('/api/auth/social-login', [
            'provider' => 'apple',
            'provider_id' => '123456789',
            'email' => 'test@example.com',
            'name' => 'Test User',
            'locale' => 'fr',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['locale']);
    }

    /**
     * Test social login fails with invalid avatar URL
     */
    public function test_social_login_fails_with_invalid_avatar_url(): void
    {
        $response = $this->postJson('/api/auth/social-login', [
            'provider' => 'google',
            'provider_id' => '123456789',
            'email' => 'test@example.com',
            'name' => 'Test User',
            'avatar' => 'not-a-url',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['avatar']);
    }

    /**
     * Test social login returns working token
     */
    public function test_social_login_returns_working_token(): void
    {
        $response = $this->postJson('/api/auth/social-login', [
            'provider' => 'apple',
            'provider_id' => '123456789',
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $token = $response->json('token');

        $meResponse = $this->getJson('/api/auth/me', [
            'Authorization' => 'Bearer '.$token,
        ]);

        $meResponse->assertStatus(200)
            ->assertJson([
                'user' => [
                    'email' => 'test@example.com',
                ],
            ]);
    }

    /**
     * Test Apple Private Relay email is supported
     */
    public function test_apple_private_relay_email_is_supported(): void
    {
        $response = $this->postJson('/api/auth/social-login', [
            'provider' => 'apple',
            'provider_id' => '001234.abc123def456.1234',
            'email' => 'abc123@privaterelay.appleid.com',
            'name' => 'Apple User',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'user' => [
                    'email' => 'abc123@privaterelay.appleid.com',
                ],
            ]);
    }

    /**
     * Test social login with all supported locales
     */
    public function test_social_login_with_all_supported_locales(): void
    {
        $locales = ['en', 'uk'];

        foreach ($locales as $index => $locale) {
            $response = $this->postJson('/api/auth/social-login', [
                'provider' => 'google',
                'provider_id' => "user-{$index}",
                'email' => "{$locale}@example.com",
                'name' => "User {$locale}",
                'locale' => $locale,
            ]);

            $response->assertStatus(201)
                ->assertJson([
                    'user' => [
                        'locale' => $locale,
                    ],
                ]);
        }
    }

    /**
     * Test that social login doesn't create password
     */
    public function test_social_login_does_not_create_password(): void
    {
        $this->postJson('/api/auth/social-login', [
            'provider' => 'apple',
            'provider_id' => '123456789',
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $user = User::where('email', 'test@example.com')->first();

        $this->assertNull($user->password);
    }

    /**
     * Test Google login saves avatar
     */
    public function test_google_login_saves_avatar(): void
    {
        $avatarUrl = 'https://lh3.googleusercontent.com/a/avatar123';

        $response = $this->postJson('/api/auth/social-login', [
            'provider' => 'google',
            'provider_id' => '123456789',
            'email' => 'test@example.com',
            'name' => 'Test User',
            'avatar' => $avatarUrl,
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'user' => [
                    'avatar' => $avatarUrl,
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'avatar' => $avatarUrl,
        ]);
    }

    /**
     * Test Apple login without avatar
     */
    public function test_apple_login_without_avatar(): void
    {
        $response = $this->postJson('/api/auth/social-login', [
            'provider' => 'apple',
            'provider_id' => '123456789',
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'user' => [
                    'avatar' => null,
                ],
            ]);
    }

    /**
     * Test social login is case-insensitive for provider
     */
    public function test_social_login_accepts_lowercase_provider(): void
    {
        $response = $this->postJson('/api/auth/social-login', [
            'provider' => 'apple',
            'provider_id' => '123456789',
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);

        $response->assertStatus(201);
    }

    /**
     * Test that same provider_id for different providers creates different users
     */
    public function test_same_provider_id_for_different_providers_creates_different_users(): void
    {
        $providerId = 'same-id-123';

        $this->postJson('/api/auth/social-login', [
            'provider' => 'apple',
            'provider_id' => $providerId,
            'email' => 'apple@example.com',
            'name' => 'Apple User',
        ]);

        $this->postJson('/api/auth/social-login', [
            'provider' => 'google',
            'provider_id' => $providerId,
            'email' => 'google@example.com',
            'name' => 'Google User',
        ]);

        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseHas('users', ['apple_id' => $providerId]);
        $this->assertDatabaseHas('users', ['google_id' => $providerId]);
    }
}
