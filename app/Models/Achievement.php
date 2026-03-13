<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'title',
        'description',
        'icon',
        'category',
        'rarity',
        'xp_reward',
        'requirements',
        'is_secret',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'requirements' => 'array',
            'is_secret' => 'boolean',
            'xp_reward' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_achievements')
            ->withPivot(['unlocked_at', 'metadata', 'is_notified'])
            ->withTimestamps();
    }
}
