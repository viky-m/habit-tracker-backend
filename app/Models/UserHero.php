<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserHero extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'hero_id',
        'level',
        'experience',
        'experience_to_next_level',
        'total_habits_completed',
        'current_streak',
        'best_streak',
        'is_active',
        'is_unlocked',
        'customization',
        'stats',
        'achievements',
        'last_active_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_unlocked' => 'boolean',
            'customization' => 'array',
            'stats' => 'array',
            'achievements' => 'array',
            'last_active_at' => 'datetime',
            'level' => 'integer',
            'experience' => 'integer',
            'experience_to_next_level' => 'integer',
            'total_habits_completed' => 'integer',
            'current_streak' => 'integer',
            'best_streak' => 'integer',
        ];
    }

    /**
     * Get the user that owns this hero instance
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the hero template
     */
    public function hero(): BelongsTo
    {
        return $this->belongsTo(Hero::class);
    }

    /**
     * Add experience points and handle level up
     */
    public function addExperience(int $amount): void
    {
        $this->experience += $amount;

        while ($this->experience >= $this->experience_to_next_level) {
            $this->levelUp();
        }

        $this->save();
    }

    /**
     * Level up the hero
     */
    protected function levelUp(): void
    {
        $this->experience -= $this->experience_to_next_level;
        $this->level++;
        $this->experience_to_next_level = $this->calculateNextLevelExperience();

        // Update stats based on new level
        $this->updateStats();
    }

    /**
     * Calculate experience needed for next level
     */
    protected function calculateNextLevelExperience(): int
    {
        // Formula: 100 * level^1.5
        return (int) (100 * pow($this->level, 1.5));
    }

    /**
     * Update hero stats based on level
     */
    protected function updateStats(): void
    {
        $baseStats = $this->hero->stats ?? [];
        $levelMultiplier = 1 + ($this->level * 0.1); // 10% increase per level

        $updatedStats = [];
        foreach ($baseStats as $stat => $value) {
            $updatedStats[$stat] = (int) ($value * $levelMultiplier);
        }

        $this->stats = $updatedStats;
    }

    /**
     * Get progress to next level as percentage
     */
    public function getLevelProgress(): float
    {
        if ($this->experience_to_next_level <= 0) {
            return 100;
        }

        return ($this->experience / $this->experience_to_next_level) * 100;
    }
}
