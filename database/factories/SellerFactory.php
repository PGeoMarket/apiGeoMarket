<?php
// SellerFactory.php
namespace Database\Factories;
use App\Models\Seller;
use Illuminate\Database\Eloquent\Factories\Factory;

class SellerFactory extends Factory
{
    protected $model = Seller::class;
    
    public function definition(): array
    {
        return [
            'nombre_tienda' => $this->faker->company() . ' ' . $this->faker->randomElement([
                'Store', 'Shop', 'Market', 'Tienda', 'Plaza', 'Centro'
            ]),
            'descripcion' => $this->faker->optional(0.7)->paragraph(3),
            'activo' => $this->faker->boolean(90),
            // user_id se asignará en el seeder
        ];
    }
}