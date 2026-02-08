<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    /**
     * Seed achievements for gamification
     */
    public function run(): void
    {
        $achievements = [
            // Habits achievements
            [
                'key' => 'first_habit',
                'title' => 'First Step',
                'description' => 'Create your first habit',
                'icon' => '🎯',
                'category' => 'habits',
                'rarity' => 'common',
                'xp_reward' => 10,
                'requirements' => ['total_habits' => 1],
                'sort_order' => 1,
            ],
            [
                'key' => 'habit_collector',
                'title' => 'Habit Collector',
                'description' => 'Create 5 different habits',
                'icon' => '📚',
                'category' => 'habits',
                'rarity' => 'common',
                'xp_reward' => 25,
                'requirements' => ['total_habits' => 5],
                'sort_order' => 2,
            ],
            [
                'key' => 'habit_master',
                'title' => 'Habit Master',
                'description' => 'Create 10 different habits',
                'icon' => '🏆',
                'category' => 'habits',
                'rarity' => 'rare',
                'xp_reward' => 50,
                'requirements' => ['total_habits' => 10],
                'sort_order' => 3,
            ],

            // Streak achievements
            [
                'key' => 'week_warrior',
                'title' => 'Week Warrior',
                'description' => 'Maintain a 7-day streak',
                'icon' => '🔥',
                'category' => 'streaks',
                'rarity' => 'common',
                'xp_reward' => 30,
                'requirements' => ['current_streak' => 7],
                'sort_order' => 10,
            ],
            [
                'key' => 'month_champion',
                'title' => 'Month Champion',
                'description' => 'Maintain a 30-day streak',
                'icon' => '⭐',
                'category' => 'streaks',
                'rarity' => 'rare',
                'xp_reward' => 100,
                'requirements' => ['current_streak' => 30],
                'sort_order' => 11,
            ],
            [
                'key' => 'century_legend',
                'title' => 'Century Legend',
                'description' => 'Maintain a 100-day streak',
                'icon' => '👑',
                'category' => 'streaks',
                'rarity' => 'epic',
                'xp_reward' => 500,
                'requirements' => ['current_streak' => 100],
                'sort_order' => 12,
            ],

            // Completion achievements
            [
                'key' => 'first_completion',
                'title' => 'Getting Started',
                'description' => 'Complete your first habit',
                'icon' => '✅',
                'category' => 'completions',
                'rarity' => 'common',
                'xp_reward' => 5,
                'requirements' => ['total_completions' => 1],
                'sort_order' => 20,
            ],
            [
                'key' => 'hundred_club',
                'title' => 'Hundred Club',
                'description' => 'Complete 100 habits',
                'icon' => '💯',
                'category' => 'completions',
                'rarity' => 'rare',
                'xp_reward' => 75,
                'requirements' => ['total_completions' => 100],
                'sort_order' => 21,
            ],
            [
                'key' => 'thousand_master',
                'title' => 'Thousand Master',
                'description' => 'Complete 1000 habits',
                'icon' => '🌟',
                'category' => 'completions',
                'rarity' => 'legendary',
                'xp_reward' => 1000,
                'requirements' => ['total_completions' => 1000],
                'sort_order' => 22,
            ],

            // Level achievements
            [
                'key' => 'level_5',
                'title' => 'Rising Star',
                'description' => 'Reach level 5',
                'icon' => '⬆️',
                'category' => 'levels',
                'rarity' => 'common',
                'xp_reward' => 20,
                'requirements' => ['hero_level' => 5],
                'sort_order' => 30,
            ],
            [
                'key' => 'level_10',
                'title' => 'Hero',
                'description' => 'Reach level 10',
                'icon' => '🦸',
                'category' => 'levels',
                'rarity' => 'rare',
                'xp_reward' => 50,
                'requirements' => ['hero_level' => 10],
                'sort_order' => 31,
            ],
            [
                'key' => 'level_20',
                'title' => 'Legend',
                'description' => 'Reach level 20',
                'icon' => '👑',
                'category' => 'levels',
                'rarity' => 'epic',
                'xp_reward' => 200,
                'requirements' => ['hero_level' => 20],
                'sort_order' => 32,
            ],

            // Special achievements
            [
                'key' => 'early_bird',
                'title' => 'Early Bird',
                'description' => 'Join the community in first month',
                'icon' => '🐦',
                'category' => 'special',
                'rarity' => 'rare',
                'xp_reward' => 50,
                'requirements' => ['days_registered' => 1],
                'is_secret' => true,
                'sort_order' => 100,
            ],
        ];

        foreach ($achievements as $achievementData) {
            Achievement::updateOrCreate(
                ['key' => $achievementData['key']],
                $achievementData
            );
        }

        $this->command->info('Achievements seeded successfully!');
    }
}
