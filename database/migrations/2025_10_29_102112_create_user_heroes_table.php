<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_heroes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('hero_id')->constrained()->onDelete('cascade');
            $table->integer('level')->default(1);
            $table->integer('experience')->default(0);
            $table->integer('experience_to_next_level')->default(100);
            $table->integer('total_habits_completed')->default(0);
            $table->integer('current_streak')->default(0);
            $table->integer('best_streak')->default(0);
            $table->boolean('is_active')->default(false); // Currently selected hero
            $table->boolean('is_unlocked')->default(true);
            $table->json('customization')->nullable(); // User's customization choices
            $table->json('stats')->nullable(); // Current stats based on level
            $table->json('achievements')->nullable(); // Unlocked achievements for this hero
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'hero_id']); // One instance of each hero per user
            $table->index(['user_id', 'is_active']);
            $table->index('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_heroes');
    }
};
