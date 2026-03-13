<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'apple_id',
        'google_id',
        'provider',
        'avatar',
        'locale',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all habits for this user
     */
    public function habits(): HasMany
    {
        return $this->hasMany(Habit::class);
    }

    /**
     * Get all habit logs for this user
     */
    public function habitLogs(): HasMany
    {
        return $this->hasMany(HabitLog::class);
    }

    /**
     * Get all heroes for this user
     */
    public function userHeroes(): HasMany
    {
        return $this->hasMany(UserHero::class);
    }

    /**
     * Get the currently active hero
     */
    public function activeHero(): HasOne
    {
        return $this->hasOne(UserHero::class)->where('is_active', true);
    }

    /**
     * Get active habits
     */
    public function activeHabits(): HasMany
    {
        return $this->habits()->where('is_active', true);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(HabitReminder::class);
    }

    public function achievements(): BelongsToMany
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
            ->withPivot(['unlocked_at', 'metadata', 'is_notified'])
            ->withTimestamps();
    }
}
