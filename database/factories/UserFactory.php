<?php
// UserFactory.php
namespace Database\Factories;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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
            'email' => $this->faker->unique()->safeEmail(),
            'password_hash' => Hash::make('password'),
            'activo' => $this->faker->boolean(97),
            // role_id se asignará en el seeder
        ];
    }
}