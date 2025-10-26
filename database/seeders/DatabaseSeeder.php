<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ne rien faire en production
        if (app()->environment('production')) {
            $this->command->info('⚠️ Seeders désactivés en production');
            return;
        }

        // Seeders seulement pour le développement local
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
