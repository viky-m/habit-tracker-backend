<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful login with valid credentials
     */
    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email', 'avatar', 'locale'],
                'token',
            ])
            ->assertJson([
                'message' => 'Login successful',
                'user' => [
                    'id' => $user->id,
                    'email' => 'test@example.com',
                ],
            ]);

        $this->assertNotNull($response->json('token'));
    }

    /**
     * Test login fails with incorrect password
     */
    public function test_login_fails_with_incorrect_password(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('correct-password'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials',
            ]);
    }

    /**
     * Test login fails with non-existent email
     */
    public function test_login_fails_with_non_existent_email(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials',
            ]);
    }

    /**
     * Test login fails without email
     */
    public function test_login_fails_without_email(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test login fails without password
     */
    public function test_login_fails_without_password(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test login fails with invalid email format
     */
    public function test_login_fails_with_invalid_email_format(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'invalid-email',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test that login returns a working token
     */
    public function test_login_returns_working_token(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $token = $response->json('token');

        // Use token to access protected endpoint
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
     * Test user can login multiple times and get different tokens
     */
    public function test_user_can_login_multiple_times_with_different_tokens(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response1 = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response2 = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $token1 = $response1->json('token');
        $token2 = $response2->json('token');

        $this->assertNotEquals($token1, $token2);

        // Both tokens should work
        $this->getJson('/api/auth/me', ['Authorization' => 'Bearer '.$token1])
            ->assertStatus(200);

        $this->getJson('/api/auth/me', ['Authorization' => 'Bearer '.$token2])
            ->assertStatus(200);
    }

    /**
     * Test login with email in different case
     */
    public function test_login_with_email_in_different_case(): void
    {
        User::factory()->create([
            'email' => 'Test@Example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'Test@Example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
    }

    /**
     * Test login returns user with correct locale
     */
    public function test_login_returns_user_with_correct_locale(): void
    {
        User::factory()->create([
            'email' => 'ukrainian@example.com',
            'password' => bcrypt('password123'),
            'locale' => 'uk',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'ukrainian@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'user' => [
                    'locale' => 'uk',
                ],
            ]);
    }

    /**
     * Test login returns user with avatar if set
     */
    public function test_login_returns_user_with_avatar_if_set(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'avatar' => 'https://example.com/avatar.jpg',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'user' => [
                    'avatar' => 'https://example.com/avatar.jpg',
                ],
            ]);
    }

    /**
     * Test login with empty password
     */
    public function test_login_fails_with_empty_password(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test login with empty email
     */
    public function test_login_fails_with_empty_email(): void
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => '',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test login fails for user registered via social login without password
     */
    public function test_login_fails_for_social_user_without_password(): void
    {
        User::factory()->create([
            'email' => 'social@example.com',
            'password' => null,
            'provider' => 'google',
            'google_id' => '123456',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'social@example.com',
            'password' => 'anypassword',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials',
            ]);
    }

    /**
     * Test that login doesn't expose whether email exists
     */
    public function test_login_error_message_is_generic(): void
    {
        User::factory()->create([
            'email' => 'existing@example.com',
            'password' => bcrypt('password123'),
        ]);

        $responseWrongPassword = $this->postJson('/api/auth/login', [
            'email' => 'existing@example.com',
            'password' => 'wrongpassword',
        ]);

        $responseWrongEmail = $this->postJson('/api/auth/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        // Both should return the same generic error message
        $this->assertEquals(
            $responseWrongPassword->json('message'),
            $responseWrongEmail->json('message')
        );
    }
}
