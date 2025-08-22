<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;
    public function definition(): array
    {
        return [
            'primer_nombre' => $this->faker->firstName(),
            'segundo_nombre' => $this->faker->optional(0.3)->firstName(),
            'primer_apellido' => $this->faker->lastName(),
            'segundo_apellido' => $this->faker->optional(0.7)->lastName(),
            'foto' => $this->faker->optional(0.6)->imageUrl(300, 300, 'people'),
            'email' => $this->faker->unique()->safeEmail(),
            'password_hash' => Hash::make('password'),
            'rol_id' => Role::factory(),
            'latitud' => $this->faker->optional(0.8)->latitude(-4.0, 12.0), // Colombia coords
            'longitud' => $this->faker->optional(0.8)->longitude(-82.0, -66.0), // Colombia coords
            'direccion_completa' => $this->faker->optional(0.8)->address(),
            'activo' => $this->faker->b
        ];
    }
}
