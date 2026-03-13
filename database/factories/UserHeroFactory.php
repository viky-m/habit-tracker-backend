<?php

namespace Database\Factories;

use App\Models\Hero;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserHero>
 */
class UserHeroFactory extends Factory
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
            'hero_id' => Hero::factory(),
            'level' => 1,
            'experience' => 0,
            'is_unlocked' => true,
            'is_active' => false,
            'customization' => null,
            'stats' => [
                'strength' => 10,
                'endurance' => 8,
                'agility' => 6,
            ],
            'achievements' => null,
            'last_active_at' => null,
        ];
    }

    /**
     * Indicate that the hero is active
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'last_active_at' => now(),
        ]);
    }
}
