<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Achievement>
 */
class AchievementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => fake()->unique()->slug(),
            'title' => fake()->sentence(3),
            'description' => fake()->sentence(),
            'icon' => fake()->randomElement(['🎯', '🔥', '⭐', '🏆', '💯']),
            'category' => fake()->randomElement(['habits', 'streaks', 'levels', 'completions']),
            'rarity' => fake()->randomElement(['common', 'rare', 'epic', 'legendary']),
            'xp_reward' => fake()->numberBetween(10, 100),
            'requirements' => ['total_habits' => 1],
            'is_secret' => false,
            'sort_order' => 0,
        ];
    }

    /**
     * Indicate that the achievement is secret
     */
    public function secret(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_secret' => true,
        ]);
    }
}
