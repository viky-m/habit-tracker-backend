<?php

namespace Database\Factories;

use App\Models\Habit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HabitReminder>
 */
class HabitReminderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'habit_id' => Habit::factory(),
            'time' => fake()->time('H:i'),
            'days' => null, // Every day by default
            'timezone' => 'UTC',
            'is_enabled' => true,
            'notification_type' => 'push',
            'message' => null,
            'last_sent_at' => null,
        ];
    }

    /**
     * Indicate that the reminder is for weekdays only
     */
    public function weekdays(): static
    {
        return $this->state(fn (array $attributes) => [
            'days' => [1, 2, 3, 4, 5],
        ]);
    }

    /**
     * Indicate that the reminder is disabled
     */
    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_enabled' => false,
        ]);
    }
}
