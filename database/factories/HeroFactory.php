<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hero>
 */
class HeroFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'description' => fake()->sentence(),
            'model_url' => '/models/'.fake()->word().'.glb',
            'thumbnail_url' => '/images/heroes/'.fake()->word().'.png',
            'rarity' => fake()->randomElement(['common', 'rare', 'epic', 'legendary']),
            'unlock_level' => fake()->numberBetween(0, 20),
            'unlock_cost' => fake()->numberBetween(0, 500),
            'is_premium' => false,
            'stats' => [
                'strength' => fake()->numberBetween(5, 20),
                'endurance' => fake()->numberBetween(5, 20),
                'agility' => fake()->numberBetween(5, 20),
            ],
            'customization_options' => null,
        ];
    }
}
