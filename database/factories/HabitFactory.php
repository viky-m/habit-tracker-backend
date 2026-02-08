<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Habit>
 */
class HabitFactory extends Factory
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
            'title' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'icon' => fake()->randomElement(['💪', '📚', '🏃', '🧘', '💧']),
            'color' => fake()->hexColor(),
            'frequency' => fake()->randomElement(['daily', 'weekly', 'monthly']),
            'frequency_days' => null,
            'target_count' => 1,
            'streak' => 0,
            'best_streak' => 0,
            'total_completions' => 0,
            'is_active' => true,
            'last_completed_at' => null,
        ];
    }

    /**
     * Indicate that the habit is inactive
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
