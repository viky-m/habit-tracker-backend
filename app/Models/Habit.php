<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Habit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'icon',
        'color',
        'frequency',
        'frequency_days',
        'target_count',
        'streak',
        'best_streak',
        'total_completions',
        'is_active',
        'last_completed_at',
    ];

    protected function casts(): array
    {
        return [
            'frequency_days' => 'array',
            'is_active' => 'boolean',
            'last_completed_at' => 'datetime',
            'streak' => 'integer',
            'best_streak' => 'integer',
            'total_completions' => 'integer',
            'target_count' => 'integer',
        ];
    }

    /**
     * Get the user that owns the habit
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all habit logs for this habit
     */
    public function logs(): HasMany
    {
        return $this->hasMany(HabitLog::class);
    }

    /**
     * Get today's log if exists
     */
    public function todayLog()
    {
        return $this->logs()->whereDate('completed_at', today())->first();
    }

    /**
     * Check if habit was completed today
     */
    public function isCompletedToday(): bool
    {
        return $this->logs()
            ->whereDate('completed_at', today())
            ->exists();
    }

    /**
     * Get completion percentage for current period
     */
    public function getCompletionRate(int $days = 30): float
    {
        $totalDays = $days;
        $completedDays = $this->logs()
            ->where('completed_at', '>=', now()->subDays($days))
            ->count();

        return $totalDays > 0 ? ($completedDays / $totalDays) * 100 : 0;
    }

    /**
     * Get habit reminders
     */
    public function reminders(): HasMany
    {
        return $this->hasMany(HabitReminder::class);
    }
}
