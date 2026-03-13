<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test authenticated user can logout
     */
    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->postJson('/api/auth/logout', [], [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logout successful',
            ]);
    }

    /**
     * Test logout fails without authentication
     */
    public function test_logout_fails_without_authentication(): void
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401);
    }

    /**
     * Test logout fails with invalid token
     */
    public function test_logout_fails_with_invalid_token(): void
    {
        $response = $this->postJson('/api/auth/logout', [], [
            'Authorization' => 'Bearer invalid-token-123',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test token is invalidated after logout
     */
    public function test_token_is_invalidated_after_logout(): void
    {
        $user = User::factory()->create();
        $tokenResult = $user->createToken('test-token');
        $token = $tokenResult->plainTextToken;
        $tokenId = $tokenResult->accessToken->id;

        // Verify token exists in database before logout
        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $tokenId,
        ]);

        // Logout
        $this->postJson('/api/auth/logout', [], [
            'Authorization' => 'Bearer '.$token,
        ])->assertStatus(200);

        // Verify token is deleted from database
        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $tokenId,
        ]);
    }

    /**
     * Test logout only invalidates current token
     */
    public function test_logout_only_invalidates_current_token(): void
    {
        $user = User::factory()->create();
        $tokenResult1 = $user->createToken('token-1');
        $token1 = $tokenResult1->plainTextToken;
        $tokenId1 = $tokenResult1->accessToken->id;

        $tokenResult2 = $user->createToken('token-2');
        $token2 = $tokenResult2->plainTextToken;
        $tokenId2 = $tokenResult2->accessToken->id;

        // Logout with token1
        $this->postJson('/api/auth/logout', [], [
            'Authorization' => 'Bearer '.$token1,
        ])->assertStatus(200);

        // Verify token1 is deleted from database
        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $tokenId1,
        ]);

        // Verify token2 still exists in database
        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $tokenId2,
        ]);
    }

    /**
     * Test user can logout multiple times from different devices
     */
    public function test_user_can_logout_from_multiple_devices(): void
    {
        $user = User::factory()->create();
        $tokenResult1 = $user->createToken('device-1');
        $tokenId1 = $tokenResult1->accessToken->id;

        $tokenResult2 = $user->createToken('device-2');
        $token2 = $tokenResult2->plainTextToken;
        $tokenId2 = $tokenResult2->accessToken->id;

        $tokenResult3 = $user->createToken('device-3');
        $tokenId3 = $tokenResult3->accessToken->id;

        // Logout from device 2
        $this->postJson('/api/auth/logout', [], [
            'Authorization' => 'Bearer '.$token2,
        ])->assertStatus(200);

        // Verify token2 is deleted but others remain
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $tokenId2]);
        $this->assertDatabaseHas('personal_access_tokens', ['id' => $tokenId1]);
        $this->assertDatabaseHas('personal_access_tokens', ['id' => $tokenId3]);
    }

    /**
     * Test logout with expired token fails
     */
    public function test_logout_with_expired_token_fails(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token');

        // Delete the token to simulate expiration
        $token->accessToken->delete();

        $response = $this->postJson('/api/auth/logout', [], [
            'Authorization' => 'Bearer '.$token->plainTextToken,
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test logout with missing Bearer prefix fails
     */
    public function test_logout_with_missing_bearer_prefix_fails(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->postJson('/api/auth/logout', [], [
            'Authorization' => $token, // Missing "Bearer "
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test logout with empty authorization header fails
     */
    public function test_logout_with_empty_authorization_header_fails(): void
    {
        $response = $this->postJson('/api/auth/logout', [], [
            'Authorization' => '',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test logout using Sanctum helper
     */
    public function test_logout_using_sanctum_helper(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logout successful',
            ]);
    }

    /**
     * Test logout removes token from database
     */
    public function test_logout_removes_token_from_database(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token');
        $tokenId = $token->accessToken->id;

        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $tokenId,
        ]);

        $this->postJson('/api/auth/logout', [], [
            'Authorization' => 'Bearer '.$token->plainTextToken,
        ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $tokenId,
        ]);
    }

    /**
     * Test token is properly removed from database after logout
     */
    public function test_token_is_removed_after_logout(): void
    {
        $user = User::factory()->create();
        $tokenResult = $user->createToken('test-token');
        $token = $tokenResult->plainTextToken;
        $tokenId = $tokenResult->accessToken->id;

        // Verify token exists
        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $tokenId,
            'tokenable_id' => $user->id,
        ]);

        // Logout
        $this->postJson('/api/auth/logout', [], [
            'Authorization' => 'Bearer '.$token,
        ])->assertStatus(200);

        // Verify token is deleted
        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $tokenId,
        ]);
    }

    /**
     * Test logout for email registered user
     */
    public function test_logout_for_email_registered_user(): void
    {
        $user = User::factory()->create([
            'provider' => 'email',
            'password' => bcrypt('password123'),
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->postJson('/api/auth/logout', [], [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response->assertStatus(200);
    }

    /**
     * Test logout for Apple registered user
     */
    public function test_logout_for_apple_registered_user(): void
    {
        $user = User::factory()->create([
            'provider' => 'apple',
            'apple_id' => '001234.abc123def456.1234',
            'password' => null,
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->postJson('/api/auth/logout', [], [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response->assertStatus(200);
    }

    /**
     * Test logout for Google registered user
     */
    public function test_logout_for_google_registered_user(): void
    {
        $user = User::factory()->create([
            'provider' => 'google',
            'google_id' => '123456789',
            'password' => null,
        ]);

        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->postJson('/api/auth/logout', [], [
            'Authorization' => 'Bearer '.$token,
        ]);

        $response->assertStatus(200);
    }

    /**
     * Test logout with token containing special characters
     */
    public function test_logout_with_malformed_token(): void
    {
        $response = $this->postJson('/api/auth/logout', [], [
            'Authorization' => 'Bearer <script>alert("xss")</script>',
        ]);

        $response->assertStatus(401);
    }

    /**
     * Test double logout fails
     */
    public function test_double_logout_fails(): void
    {
        $user = User::factory()->create();
        $tokenResult = $user->createToken('test-token');
        $token = $tokenResult->plainTextToken;
        $tokenId = $tokenResult->accessToken->id;

        // First logout
        $this->postJson('/api/auth/logout', [], [
            'Authorization' => 'Bearer '.$token,
        ])->assertStatus(200);

        // Verify token is deleted
        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $tokenId,
        ]);

        // Verify user has no tokens left
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    /**
     * Test logout response structure
     */
    public function test_logout_response_structure(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
            ])
            ->assertJson([
                'message' => 'Logout successful',
            ]);
    }
}
