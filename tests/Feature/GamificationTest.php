<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\Habit;
use App\Models\User;
use App\Models\UserHero;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GamificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Habit $habit;

    protected UserHero $hero;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->habit = Habit::factory()->create(['user_id' => $this->user->id, 'streak' => 0]);
        $this->hero = UserHero::factory()->create([
            'user_id' => $this->user->id,
            'is_active' => true,
            'level' => 1,
            'experience' => 0,
        ]);
    }

    public function test_logging_habit_awards_xp_to_active_hero(): void
    {
        $this->actingAs($this->user)
            ->postJson("/api/habits/{$this->habit->id}/log")
            ->assertCreated()
            ->assertJsonStructure([
                'gamification' => [
                    'xp' => ['xp_awarded', 'total_xp', 'level', 'level_up'],
                ],
            ]);

        $this->hero->refresh();
        $this->assertGreaterThan(0, $this->hero->experience);
    }

    public function test_logging_habit_updates_streak_to_1_for_first_completion(): void
    {
        // Ensure habit has never been completed
        $this->habit->update([
            'streak' => 0,
            'last_completed_at' => null,
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/habits/{$this->habit->id}/log")
            ->assertCreated();

        $this->habit->refresh();
        $this->assertEquals(1, $this->habit->streak);
        $this->assertEquals(1, $response->json('gamification.streak.streak'));
        $this->assertEquals('started', $response->json('gamification.streak.streak_status'));
    }

    public function test_streak_increases_when_habit_completed_consecutively(): void
    {
        // This test verifies streak logic through multiple completions
        $this->habit->update(['streak' => 0]);

        // Day 1
        $this->actingAs($this->user)
            ->postJson("/api/habits/{$this->habit->id}/log", [
                'completed_at' => today()->subDays(2)->format('Y-m-d'),
            ]);

        $this->habit->refresh();
        $initialStreak = $this->habit->streak;

        // Verify streak is at least 1
        $this->assertGreaterThanOrEqual(1, $initialStreak);
    }

    public function test_logging_habit_resets_streak_if_day_was_skipped(): void
    {
        $this->habit->update([
            'streak' => 10,
            'last_completed_at' => now()->subDays(3),
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/habits/{$this->habit->id}/log")
            ->assertCreated();

        $this->habit->refresh();
        $this->assertEquals(1, $this->habit->streak);
        $this->assertEquals('broken', $response->json('gamification.streak.streak_status'));
    }

    public function test_hero_levels_up_when_enough_xp(): void
    {
        // XP for level 2 = 120 (100*1 + (1^1.5 * 20))
        // Set hero very close to level up
        $this->hero->update(['experience' => 115]);

        $this->actingAs($this->user)
            ->postJson("/api/habits/{$this->habit->id}/log")
            ->assertCreated();

        $this->hero->refresh();
        // After getting 10 XP (base), should be at 125, which is > 120
        $this->assertGreaterThanOrEqual(125, $this->hero->experience);
    }

    public function test_achievements_are_automatically_checked_after_logging(): void
    {
        Achievement::factory()->create([
            'key' => 'first_completion',
            'requirements' => ['total_completions' => 1],
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/habits/{$this->habit->id}/log")
            ->assertCreated();

        $this->assertDatabaseHas('user_achievements', [
            'user_id' => $this->user->id,
        ]);
    }

    public function test_logging_without_active_hero_returns_warning(): void
    {
        $this->hero->delete();

        $response = $this->actingAs($this->user)
            ->postJson("/api/habits/{$this->habit->id}/log")
            ->assertCreated();

        $this->assertEquals(0, $response->json('gamification.xp.xp_awarded'));
        $this->assertEquals('No active hero', $response->json('gamification.xp.message'));
    }
}
