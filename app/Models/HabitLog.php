<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HabitLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'habit_id',
        'user_id',
        'completed_at',
        'note',
        'count',
    ];

    protected function casts(): array
    {
        return [
            'completed_at' => 'date',
            'count' => 'integer',
        ];
    }

    /**
     * Get the habit that owns this log
     */
    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }

    /**
     * Get the user that owns this log
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
