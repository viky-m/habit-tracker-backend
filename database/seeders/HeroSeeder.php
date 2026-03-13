<?php

namespace Database\Seeders;

use App\Models\Hero;
use Illuminate\Database\Seeder;

class HeroSeeder extends Seeder
{
    /**
     * Seed starter heroes for gamification
     */
    public function run(): void
    {
        $heroes = [
            [
                'name' => 'Warrior',
                'description' => 'A brave warrior embarking on a journey of discipline and strength',
                'model_url' => '/models/warrior.glb',
                'thumbnail_url' => '/images/heroes/warrior.png',
                'rarity' => 'common',
                'unlock_level' => 0,
                'unlock_cost' => 0,
                'is_premium' => false,
                'stats' => [
                    'strength' => 10,
                    'endurance' => 8,
                    'agility' => 6,
                ],
                'customization_options' => [
                    'colors' => ['red', 'blue', 'green'],
                    'armors' => ['basic'],
                ],
            ],
            [
                'name' => 'Sage',
                'description' => 'A wise sage focused on mindfulness and mental clarity',
                'model_url' => '/models/sage.glb',
                'thumbnail_url' => '/images/heroes/sage.png',
                'rarity' => 'common',
                'unlock_level' => 5,
                'unlock_cost' => 100,
                'is_premium' => false,
                'stats' => [
                    'wisdom' => 12,
                    'focus' => 10,
                    'patience' => 8,
                ],
                'customization_options' => [
                    'robes' => ['white', 'blue', 'purple'],
                    'staffs' => ['wooden', 'crystal'],
                ],
            ],
            [
                'name' => 'Guardian',
                'description' => 'A steadfast guardian protecting your streak and progress',
                'model_url' => '/models/guardian.glb',
                'thumbnail_url' => '/images/heroes/guardian.png',
                'rarity' => 'rare',
                'unlock_level' => 10,
                'unlock_cost' => 250,
                'is_premium' => false,
                'stats' => [
                    'defense' => 15,
                    'endurance' => 12,
                    'resilience' => 10,
                ],
                'customization_options' => [
                    'shields' => ['bronze', 'silver', 'gold'],
                    'helmets' => ['basic', 'advanced'],
                ],
            ],
            [
                'name' => 'Phoenix',
                'description' => 'A mythical phoenix rising from challenges with renewed determination',
                'model_url' => '/models/phoenix.glb',
                'thumbnail_url' => '/images/heroes/phoenix.png',
                'rarity' => 'epic',
                'unlock_level' => 20,
                'unlock_cost' => 500,
                'is_premium' => true,
                'stats' => [
                    'rebirth' => 20,
                    'inspiration' => 18,
                    'transformation' => 15,
                ],
                'customization_options' => [
                    'flames' => ['orange', 'blue', 'rainbow'],
                    'wings' => ['fire', 'ice', 'lightning'],
                ],
            ],
        ];

        foreach ($heroes as $heroData) {
            Hero::updateOrCreate(
                ['name' => $heroData['name']],
                $heroData
            );
        }

        $this->command->info('Heroes seeded successfully!');
    }
}
