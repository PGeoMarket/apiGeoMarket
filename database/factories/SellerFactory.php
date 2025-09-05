<?php

namespace Database\Factories;

use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SellerFactory extends Factory
{
    protected $model = Seller::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nombre_tienda' => $this->faker->company() . ' ' . $this->faker->randomElement([
                'Store', 'Shop', 'Market', 'Tienda'
            ]),
            'descripcion' => $this->faker->optional(0.7)->paragraph(3),
            'activo' => $this->faker->boolean(90),
        ];
    }
}
