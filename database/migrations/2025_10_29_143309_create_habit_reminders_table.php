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
        Schema::create('habit_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('habit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->time('time'); // Час нагадування (наприклад, 09:00)
            $table->json('days')->nullable(); // Дні тижня [1,2,3,4,5] (1=Пн, 7=Нд) або null для всіх днів
            $table->string('timezone')->default('UTC'); // Часовий пояс користувача
            $table->boolean('is_enabled')->default(true);
            $table->string('notification_type')->default('push'); // push, email, both
            $table->text('message')->nullable(); // Кастомне повідомлення
            $table->timestamp('last_sent_at')->nullable(); // Коли останній раз надіслано
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
