<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful user registration with valid data
     */
    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email', 'avatar', 'locale'],
                'token',
            ])
            ->assertJson([
                'message' => 'User registered successfully',
                'user' => [
                    'name' => 'Test User',
                    'email' => 'test@example.com',
                    'locale' => 'en',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'provider' => 'email',
        ]);
    }

    /**
     * Test user registration with custom locale
     */
    public function test_user_can_register_with_custom_locale(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Ukrainian User',
            'email' => 'ukrainian@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
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
     * Test registration fails without name
     */
    public function test_registration_fails_without_name(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test registration fails without email
     */
    public function test_registration_fails_without_email(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test registration fails with invalid email format
     */
    public function test_registration_fails_with_invalid_email(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test registration fails with duplicate email
     */
    public function test_registration_fails_with_duplicate_email(): void
    {
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test registration fails without password
     */
    public function test_registration_fails_without_password(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test registration fails when password confirmation doesn't match
     */
    public function test_registration_fails_when_password_confirmation_does_not_match(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test registration fails with short password
     */
    public function test_registration_fails_with_short_password(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test registration fails with invalid locale
     */
    public function test_registration_fails_with_invalid_locale(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'locale' => 'invalid',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['locale']);
    }

    /**
     * Test that password is hashed in database
     */
    public function test_password_is_hashed_in_database(): void
    {
        $plainPassword = 'password123';

        $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => $plainPassword,
            'password_confirmation' => $plainPassword,
        ]);

        $user = User::where('email', 'test@example.com')->first();

        $this->assertNotEquals($plainPassword, $user->password);
        $this->assertTrue(\Hash::check($plainPassword, $user->password));
    }

    /**
     * Test that registered user receives a valid token
     */
    public function test_registered_user_receives_valid_token(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $token = $response->json('token');

        $this->assertNotNull($token);
        $this->assertIsString($token);

        // Test that token works for authenticated endpoints
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
     * Test registration with all supported locales
     */
    public function test_registration_with_all_supported_locales(): void
    {
        $locales = ['en', 'uk'];

        foreach ($locales as $locale) {
            $response = $this->postJson('/api/auth/register', [
                'name' => "User {$locale}",
                'email' => "{$locale}@example.com",
                'password' => 'password123',
                'password_confirmation' => 'password123',
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
     * Test that name can contain special characters
     */
    public function test_name_can_contain_special_characters(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => "O'Brien-Smith ÄÖÜ 中文",
            'email' => 'special@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'user' => [
                    'name' => "O'Brien-Smith ÄÖÜ 中文",
                ],
            ]);
    }

    /**
     * Test registration fails when name is too long
     */
    public function test_registration_fails_when_name_is_too_long(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => str_repeat('a', 256),
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test registration fails when email is too long
     */
    public function test_registration_fails_when_email_is_too_long(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => str_repeat('a', 250).'@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
