<?php
namespace Database\Factories;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
class CategoryFactory extends Factory
{
 protected $model = Category::class;
 public function definition(): array
 {
 return [
 'categoria' => $this->faker->randomElement([
 'Electrónicos', 'Ropa y Moda', 'Hogar y Jardín', 'Deportes',
 'Automóviles', 'Libros', 'Salud y Belleza', 'Juguetes',
 'Instrumentos Musicales', 'Comida y Bebidas', 'Mascotas',
 'Arte y Manualidades', 'Tecnología', 'Inmuebles'
 ]),
 ];
 }
}