<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Compte>
 */
class CompteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         return [
            'id' => Str::uuid(),
            'numero_compte' => 'ACC-' . strtoupper(Str::random(10)),
            'user_id' => User::factory(),
            'titulaire' => $this->faker->name(),
            'type' => $this->faker->randomElement(['epargne', 'cheque']),
            'devise' => 'FCFA',
            'statut' => $this->faker->randomElement(['actif', 'bloque', 'ferme']),
            'derniere_modification' => now(),
            'version' => 1,
        ];
    }
}

