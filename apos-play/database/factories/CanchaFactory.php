<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cancha>
 */
class CanchaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'nombre' => fake()->company(),
            'direccion' => fake()->address(),
            'precio' => fake()->randomFloat(2, 2000, 15000),
            'tipo' => fake()->randomElement(['futbol', 'padel']),
            'cantidad_jugadores' => fake()->numberBetween(4, 14),
        ];
    }
}
