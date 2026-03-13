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
        Schema::create('heroes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Hero name (e.g., "Warrior", "Mage", "Archer")
            $table->text('description')->nullable();
            $table->string('model_url'); // URL to 3D model (BunnyCDN or Tripo AI)
            $table->string('thumbnail_url')->nullable(); // Preview image
            $table->enum('rarity', ['common', 'rare', 'epic', 'legendary'])->default('common');
            $table->integer('unlock_level')->default(1); // Level required to unlock
            $table->integer('unlock_cost')->default(0); // Cost in points to unlock (0 = free)
            $table->boolean('is_premium')->default(false); // Requires premium subscription
            $table->boolean('is_active')->default(true);
            $table->json('stats')->nullable(); // Base stats: {"strength": 10, "agility": 15, ...}
            $table->json('customization_options')->nullable(); // Available customizations
            $table->timestamps();

            $table->index(['is_active', 'rarity']);
            $table->index('unlock_level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heroes');
    }
};
