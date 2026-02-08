<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Habit;
use App\Models\User;
use App\Models\UserHero;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AchievementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_user_can_get_all_available_achievements(): void
    {
        Achievement::factory()->count(5)->create(['is_secret' => false]);

        $this->actingAs($this->user)
            ->getJson('/api/achievements')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'key', 'title', 'description', 'icon', 'category', 'rarity', 'xp_reward'],
                ],
            ])
            ->assertJsonCount(5, 'data');
    }

    public function test_secret_achievements_are_hidden_from_list(): void
    {
        Achievement::factory()->create(['is_secret' => true, 'key' => 'secret_one']);
        Achievement::factory()->create(['is_secret' => false, 'key' => 'public_one']);

        $response = $this->actingAs($this->user)
            ->getJson('/api/achievements')
            ->assertOk()
            ->json('data');

        $keys = collect($response)->pluck('key');
        $this->assertNotContains('secret_one', $keys);
        $this->assertContains('public_one', $keys);
    }

    public function test_user_can_get_their_unlocked_achievements(): void
    {
        $achievement = Achievement::factory()->create();

        $this->user->achievements()->attach($achievement->id, [
            'unlocked_at' => now(),
        ]);

        $this->actingAs($this->user)
            ->getJson('/api/achievements/user')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['title' => $achievement->title]);
    }

    public function test_user_can_check_for_new_achievements(): void
    {
        $achievement = Achievement::factory()->create([
            'key' => 'first_habit',
            'requirements' => ['total_habits' => 1],
        ]);

        Habit::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
            ->postJson('/api/achievements/check')
            ->assertOk()
            ->assertJson(['count' => 1]);

        $this->assertDatabaseHas('user_achievements', [
            'user_id' => $this->user->id,
            'achievement_id' => $achievement->id,
        ]);
    }

    public function test_achievement_awards_xp_to_active_hero(): void
    {
        $hero = UserHero::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => true,
            'experience' => 0,
        ]);

        $achievement = Achievement::factory()->create([
            'key' => 'test_achievement',
            'requirements' => ['total_habits' => 1],
            'xp_reward' => 50,
        ]);

        Habit::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)->postJson('/api/achievements/check');

        $hero->refresh();
        $this->assertEquals(50, $hero->experience);
    }

    public function test_already_unlocked_achievements_are_not_unlocked_again(): void
    {
        $achievement = Achievement::factory()->create([
            'requirements' => ['total_habits' => 1],
        ]);

        Habit::factory()->create(['user_id' => $this->user->id]);

        // First check
        $this->actingAs($this->user)
            ->postJson('/api/achievements/check')
            ->assertJson(['count' => 1]);

        // Second check
        $this->actingAs($this->user)
            ->postJson('/api/achievements/check')
            ->assertJson(['count' => 0]);
    }
}
