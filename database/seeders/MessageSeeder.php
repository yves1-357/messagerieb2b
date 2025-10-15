<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Hash;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer quelques utilisateurs de test
        $user1 = User::create([
            'name' => 'Alice Martin',
            'email' => 'alice@example.com',
            'username' => 'alice_martin',
            'bio' => 'Développeuse passionnée 💻',
            'password' => Hash::make('password'),
            'is_online' => true,
            'notifications_enabled' => true,
        ]);

        $user2 = User::create([
            'name' => 'Bob Dupont',
            'email' => 'bob@example.com',
            'username' => 'bob_dupont',
            'bio' => 'Designer UI/UX créatif 🎨',
            'password' => Hash::make('password'),
            'is_online' => false,
            'last_seen' => now()->subMinutes(30),
            'notifications_enabled' => true,
        ]);

        $user3 = User::create([
            'name' => 'Claire Moreau',
            'email' => 'claire@example.com',
            'username' => 'claire_moreau',
            'bio' => 'Chef de projet agile 🚀',
            'password' => Hash::make('password'),
            'is_online' => true,
            'notifications_enabled' => false,
        ]);

        // Créer une conversation privée entre Alice et Bob
        $privateConv = Conversation::create([
            'type' => 'private',
            'created_by' => $user1->id,
        ]);

        // Ajouter les participants
        $privateConv->users()->attach($user1->id, ['role' => 'member']);
        $privateConv->users()->attach($user2->id, ['role' => 'member']);

        // Créer quelques messages dans la conversation privée
        Message::create([
            'conversation_id' => $privateConv->id,
            'user_id' => $user1->id,
            'content' => 'Salut Bob ! Comment ça va ?',
            'type' => 'text',
        ]);

        Message::create([
            'conversation_id' => $privateConv->id,
            'user_id' => $user2->id,
            'content' => 'Salut Alice ! Ça va bien merci, et toi ?',
            'type' => 'text',
            'read_at' => now()->subMinutes(5),
        ]);

        $lastMessage = Message::create([
            'conversation_id' => $privateConv->id,
            'user_id' => $user1->id,
            'content' => 'Super ! Tu as avancé sur le projet ?',
            'type' => 'text',
        ]);

        // Créer un groupe
        $groupConv = Conversation::create([
            'name' => 'Équipe Dev 💻',
            'description' => 'Discussion de l\'équipe de développement',
            'type' => 'group',
            'created_by' => $user1->id,
        ]);

        // Ajouter les participants au groupe
        $groupConv->users()->attach($user1->id, ['role' => 'admin']);
        $groupConv->users()->attach($user2->id, ['role' => 'member']);
        $groupConv->users()->attach($user3->id, ['role' => 'member']);

        // Messages dans le groupe
        Message::create([
            'conversation_id' => $groupConv->id,
            'user_id' => $user1->id,
            'content' => 'Bienvenue dans le groupe équipe dev ! 🎉',
            'type' => 'text',
        ]);

        Message::create([
            'conversation_id' => $groupConv->id,
            'user_id' => $user2->id,
            'content' => 'Merci Alice ! Hâte de collaborer avec vous',
            'type' => 'text',
            'read_at' => now()->subMinutes(10),
        ]);

        Message::create([
            'conversation_id' => $groupConv->id,
            'user_id' => $user3->id,
            'content' => 'Parfait ! Quand est-ce qu\'on programme la première réunion ?',
            'type' => 'text',
        ]);

        // Message de réponse
        Message::create([
            'conversation_id' => $groupConv->id,
            'user_id' => $user1->id,
            'content' => 'Je propose demain à 14h, ça vous va ?',
            'type' => 'text',
            'reply_to' => 3, // Réponse au message de Claire
        ]);
    }
}
