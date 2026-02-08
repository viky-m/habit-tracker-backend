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
        Schema::table('users', function (Blueprint $table) {
            $table->string('apple_id')->nullable()->unique()->after('email');
            $table->string('google_id')->nullable()->unique()->after('apple_id');
            $table->string('provider')->nullable()->after('google_id'); // 'email', 'apple', 'google'
            $table->string('avatar')->nullable()->after('provider');
            $table->string('locale', 5)->default('en')->after('avatar'); // en, uk
            $table->string('password')->nullable()->change(); // Make password optional for social login
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['apple_id', 'google_id', 'provider', 'avatar', 'locale']);
        });
    }
};
