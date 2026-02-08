<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hero extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'model_url',
        'thumbnail_url',
        'rarity',
        'unlock_level',
        'unlock_cost',
        'is_premium',
        'is_active',
        'stats',
        'customization_options',
    ];

    protected function casts(): array
    {
        return [
            'is_premium' => 'boolean',
            'is_active' => 'boolean',
            'stats' => 'array',
            'customization_options' => 'array',
            'unlock_level' => 'integer',
            'unlock_cost' => 'integer',
        ];
    }

    /**
     * Get all user instances of this hero
     */
    public function userHeroes(): HasMany
    {
        return $this->hasMany(UserHero::class);
    }

    /**
     * Check if user has unlocked this hero
     */
    public function isUnlockedByUser(int $userId): bool
    {
        return $this->userHeroes()
            ->where('user_id', $userId)
            ->where('is_unlocked', true)
            ->exists();
    }

    /**
     * Get hero instance for specific user
     */
    public function forUser(int $userId)
    {
        return $this->userHeroes()
            ->where('user_id', $userId)
            ->first();
    }
}
