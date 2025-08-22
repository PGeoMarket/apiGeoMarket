<?php

namespace Database\Factories;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    protected $model = Role::class;
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->randomElement(['Admin', 'Vendedor', 'Usuario', 'Moderador']),
            'permisos' => json_encode([
                'read' => $this->faker->boolean(80),
                'write' => $this->faker->boolean(60),
                'delete' => $this->faker->boolean(30),
                'admin' => $this->faker->boolean(20),
            ]),
        ];
    }
}
