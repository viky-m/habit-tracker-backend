<?php

namespace Tests\Feature;

use App\Models\Habit;
use App\Models\HabitReminder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HabitReminderTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Habit $habit;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->habit = Habit::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_user_can_create_reminder_for_their_habit(): void
    {
        $this->actingAs($this->user)
            ->postJson('/api/reminders', [
                'habit_id' => $this->habit->id,
                'time' => '09:00',
                'days' => [1, 2, 3, 4, 5],
                'timezone' => 'Europe/Kyiv',
                'notification_type' => 'push',
            ])
            ->assertCreated()
            ->assertJsonStructure([
                'data' => ['id', 'habit_id', 'time', 'days', 'is_enabled'],
            ]);

        $this->assertDatabaseHas('habit_reminders', [
            'habit_id' => $this->habit->id,
            'user_id' => $this->user->id,
        ]);
    }

    public function test_reminder_validates_required_fields(): void
    {
        $this->actingAs($this->user)
            ->postJson('/api/reminders', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['habit_id', 'time']);
    }

    public function test_user_can_get_their_reminders(): void
    {
        HabitReminder::factory()->count(3)->create([
            'user_id' => $this->user->id,
            'habit_id' => $this->habit->id,
        ]);

        $this->actingAs($this->user)
            ->getJson('/api/reminders')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_user_can_update_their_reminder(): void
    {
        $reminder = HabitReminder::factory()->create([
            'user_id' => $this->user->id,
            'habit_id' => $this->habit->id,
            'time' => '09:00',
        ]);

        $this->actingAs($this->user)
            ->putJson("/api/reminders/{$reminder->id}", [
                'time' => '18:00',
                'is_enabled' => false,
            ])
            ->assertOk();

        $reminder->refresh();
        $this->assertEquals('18:00', substr($reminder->time, 0, 5));
        $this->assertFalse($reminder->is_enabled);
    }

    public function test_user_can_delete_their_reminder(): void
    {
        $reminder = HabitReminder::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $this->actingAs($this->user)
            ->deleteJson("/api/reminders/{$reminder->id}")
            ->assertOk();

        $this->assertNull(HabitReminder::find($reminder->id));
    }

    public function test_user_cannot_access_other_users_reminders(): void
    {
        $otherUser = User::factory()->create();
        $otherReminder = HabitReminder::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $this->actingAs($this->user)
            ->putJson("/api/reminders/{$otherReminder->id}", ['is_enabled' => false])
            ->assertForbidden();
    }
}
