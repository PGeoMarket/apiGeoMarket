<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         return [
            'nombre' => $this->faker->randomElement(['Admin', 'Vendedor', 'Comprador', 'Moderador']),
            'permisos' => json_encode([
                'create_posts' => $this->faker->boolean(),
                'edit_posts' => $this->faker->boolean(),
                'delete_posts' => $this->faker->boolean(),
                'manage_users' => $this->faker->boolean(),
            ]),
        ];
    }
}
