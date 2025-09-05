<?php
namespace Database\Factories;
use App\Models\Publication;
use App\Models\Seller;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
class PublicationFactory extends Factory
{
 protected $model = Publication::class;
 public function definition(): array
 {
 return [
 'titulo' => $this->faker->sentence(4),
 'precio' => $this->faker->numberBetween(10000, 5000000), // Pesos colombianos
 'descripcion' => $this->faker->paragraphs(2, true),
 'imagen' => $this->faker->imageUrl(600, 400, 'products'),
 'visibilidad' => $this->faker->boolean(85),
 'seller_id' => Seller::factory(),
 'category_id' => Category::factory(),
 ];
 }
}