<?php

namespace Database\Seeders;

use App\Models\Habit;
use App\Models\User;
use Illuminate\Database\Seeder;

class HabitSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->first();
        if (! $user) {
            return;
        }

        $habits = [
            [
                'title' => 'Morning Jog',
                'description' => 'Run 2km every morning',
                'icon' => '🏃',
                'color' => '#f59e0b',
                'frequency' => 'daily',
            ],
            [
                'title' => 'Read Book',
                'description' => 'Read 30 minutes',
                'icon' => '📚',
                'color' => '#3b82f6',
                'frequency' => 'daily',
            ],
            [
                'title' => 'Drink Water',
                'description' => 'Drink 2L of water',
                'icon' => '💧',
                'color' => '#06b6d4',
                'frequency' => 'daily',
            ],
            [
                'title' => 'Weekly Review',
                'description' => 'Review goals and progress',
                'icon' => '📊',
                'color' => '#8b5cf6',
                'frequency' => 'weekly',
            ],
        ];

        foreach ($habits as $habitData) {
            Habit::create([
                'user_id' => $user->id,
                ...$habitData,
            ]);
        }
    }
}
