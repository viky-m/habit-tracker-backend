<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('habit_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('habit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->time('time'); // Reminder time (e.g., 09:00)
            $table->json('days')->nullable(); // Days of week [1,2,3,4,5] (1=Mon, 7=Sun) or null for all days
            $table->string('timezone')->default('UTC'); // User timezone
            $table->boolean('is_enabled')->default(true);
            $table->string('notification_type')->default('push'); // push, email, both
            $table->text('message')->nullable(); // Custom message
            $table->timestamp('last_sent_at')->nullable(); // When last sent
            $table->timestamps();

            $table->index(['user_id', 'is_enabled']);
            $table->index('time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habit_reminders');
    }
};
