<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'nom' => $this->faker->lastName(),
            'prenom' => $this->faker->firstName(),
            'email' => $this->faker->unique()->safeEmail(),
            'telephone' => $this->faker->unique()->numerify('7########'),
            'adresse' => $this->faker->address(),
            'nci' => strtoupper($this->faker->bothify('??######??')),
            'password' => Hash::make('password123'),
            'is_verified' => 'true',
        ];
    }
}

