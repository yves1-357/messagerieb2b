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
        Schema::table('conversations', function (Blueprint $table) {
            // Renommer colonne name en name_group si elle existe
            if (Schema::hasColumn('conversations', 'name') && !Schema::hasColumn('conversations', 'name_group')) {
                $table->renameColumn('name', 'name_group');
            }
            // Ajouter name_group si elle n'existe pas du tout
            if (!Schema::hasColumn('conversations', 'name_group') && !Schema::hasColumn('conversations', 'name')) {
                $table->string('name_group')->nullable()->after('id');
            }
            // Ajouter is_group
            if (!Schema::hasColumn('conversations', 'is_group')) {
                $table->boolean('is_group')->default(false)->after('name_group');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            // Supprimer seulement is_group car name existe déjà
            if (Schema::hasColumn('conversations', 'is_group')) {
                $table->dropColumn('is_group');
            }
            // Renommer name_group vers name si elle existe
            if (Schema::hasColumn('conversations', 'name_group')) {
                $table->renameColumn('name_group', 'name');
            }
        });
    }
};
