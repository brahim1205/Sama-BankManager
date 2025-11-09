<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Compte;
use App\Models\Transaction;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Pour PostgreSQL, on désactive temporairement les contraintes de clé étrangère
        Schema::disableForeignKeyConstraints();
        
        // Nettoyer la table users
        DB::table('users')->truncate();

       

        // Création des autres utilisateurs
        User::factory()
            ->count(2)
            ->create([
                'password' => 'password123',
            ]);

        // Réactiver les contraintes de clé étrangère
        Schema::enableForeignKeyConstraints();
    }
}