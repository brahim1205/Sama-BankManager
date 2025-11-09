<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Str;

class ClientSeeder extends Seeder
{
    public function run(): void
    {

        $clientUser = User::factory()->create([
            'id' => Str::uuid(),
            'nom' => 'ndiaye',
            'prenom' => 'modou',
            'telephone' => '778899001',
            'adresse' => 'Thiès, Sénégal',
            'nci' => '1987654321098',
            'email' => 'client@gmail.com',
            'password' => 'client123',
            'is_verified' => 'true'
        ]);

        Client::create([
            'id' => Str::uuid(),
            'user_id' => $clientUser->id,
        ]);
    }
}