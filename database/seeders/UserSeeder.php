<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer des utilisateurs de test
        User::create([
            'name' => 'Alice Martin',
            'email' => 'alice@example.com',
            'password' => Hash::make('password'),
            'status' => 'online',
            'last_seen_at' => now(),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Bob Dupont',
            'email' => 'bob@example.com',
            'password' => Hash::make('password'),
            'status' => 'online',
            'last_seen_at' => now(),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Claire Durand',
            'email' => 'claire@example.com',
            'password' => Hash::make('password'),
            'status' => 'offline',
            'last_seen_at' => now()->subMinutes(10),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'David Moreau',
            'email' => 'david@example.com',
            'password' => Hash::make('password'),
            'status' => 'online',
            'last_seen_at' => now(),
            'email_verified_at' => now(),
        ]);

        $this->command->info('Utilisateurs de test créés avec succès !');
    }
}
