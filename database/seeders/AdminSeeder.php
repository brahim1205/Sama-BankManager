<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Créer un utilisateur admin avec des identifiants connus
        $adminUser = User::create([
            'id' => Str::uuid(),
            'nom' => 'Aidara',
            'prenom' => 'Ibrahima',
            'email' => 'admin@gmail.com',
            'telephone' => '766178284',
            'adresse' => 'Dakar, Sénégal',
            'nci' => '1234567890123',
            'password' => 'admin123', 
            'is_verified' => 'true'
        ]);

        // Créer l'enregistrement admin associé
        Admin::create([
            'id' => Str::uuid(),
            'user_id' => $adminUser->id
        ]);

    }
}