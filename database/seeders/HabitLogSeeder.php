<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Habit;
use Illuminate\Database\Seeder;

class HabitLogSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->first();
        if (!$user)
            return;

        $habits = $user->habits;

        foreach ($habits as $habit) {
            // Create a streak for "Morning Jog" or "Read Book"
            if (in_array($habit->title, ['Morning Jog', 'Read Book'])) {
                for ($i = 4; $i >= 0; $i--) {
                    $date = now()->subDays($i);
                    $habit->logs()->create([
                        'user_id' => $user->id,
                        'completed_at' => $date,
                        'count' => 1,
                    ]);
                }
                // Update streak manually for seeding purposes (or rely on recalculation if available)
                $habit->update([
                    'streak' => 5,
                    'best_streak' => 5,
                    'total_completions' => 5,
                    'last_completed_at' => now(),
                    'is_active' => true,
                ]);
            }

            // Random logs for Water
            if ($habit->title === 'Drink Water') {
                $days = [0, 2, 3, 5, 6, 9];
                foreach ($days as $day) {
                    $date = now()->subDays($day);
                    $habit->logs()->create([
                        'user_id' => $user->id,
                        'completed_at' => $date,
                        'count' => 1,
                    ]);
                }
                $habit->update([
                    'streak' => 1, // Reset because of gaps
                    'best_streak' => 2,
                    'total_completions' => count($days),
                    'last_completed_at' => now(),
                    'is_active' => true,
                ]);
            }
        }
    }
}
