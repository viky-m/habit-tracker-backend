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
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // first_habit, week_streak, level_10, etc.
            $table->string('title');
            $table->text('description');
            $table->string('icon')->nullable(); // Emoji або URL
            $table->string('category'); // habits, streaks, levels, social
            $table->string('rarity')->default('common'); // common, rare, epic, legendary
            $table->integer('xp_reward')->default(0); // Бонус XP за досягнення
            $table->json('requirements')->nullable(); // Умови для розблокування
            $table->boolean('is_secret')->default(false); // Прихані досягнення
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('category');
            $table->index('rarity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
