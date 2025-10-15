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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('name');
            $table->string('avatar')->nullable()->after('username');
            $table->text('bio')->nullable()->after('avatar');
            $table->boolean('is_online')->default(false)->after('bio');
            $table->timestamp('last_seen')->nullable()->after('is_online');
            $table->boolean('notifications_enabled')->default(true)->after('last_seen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'avatar',
                'bio',
                'is_online',
                'last_seen',
                'notifications_enabled'
            ]);
        });
    }
};
