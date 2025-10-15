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
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // Nom du groupe ou null pour conversation privée
            $table->text('description')->nullable(); // Description du groupe
            $table->enum('type', ['private', 'group'])->default('private');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('avatar')->nullable(); // Avatar du groupe
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
