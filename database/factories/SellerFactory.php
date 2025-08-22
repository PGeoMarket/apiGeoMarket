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
                'Store',
                'Shop',
                'Market',
                'Tienda'
            ]),
            'descripcion' => $this->faker->optional(0.7)->paragraph(3),
            'foto_portada' => $this->faker->optional(0.5)->imageUrl(800, 400, 'business'),
            'latitud_tienda' => $this->faker->optional(0.9)->latitude(-4.0, 12.0),
            'longitud_tienda' => $this->faker->optional(0.9)->longitude(-82.0, -66.0),
            'direccion_tienda' => $this->faker->optional(0.9)->address(),
            'activo' => $this->faker->boolean(90),
        ];
    }
}
