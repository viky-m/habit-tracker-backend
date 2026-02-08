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
        Schema::create('habit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('habit_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('completed_at'); // Date when habit was completed
            $table->text('note')->nullable(); // Optional note for this completion
            $table->integer('count')->default(1); // How many times completed on this day
            $table->timestamps();

            $table->unique(['habit_id', 'completed_at']); // One log per habit per day
            $table->index(['user_id', 'completed_at']);
            $table->index('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habit_logs');
    }
};
