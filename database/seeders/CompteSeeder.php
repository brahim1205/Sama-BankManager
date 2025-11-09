<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Compte;

class CompteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        if ($users->isEmpty()) {
            $users = User::factory(10)->create();
        }

        // Crée 20 comptes aléatoires assignés à des utilisateurs existants
        foreach (range(1, 20) as $index) {
            Compte::factory()->create([
                'user_id' => $users->random()->id
            ]);
        }
    }
}