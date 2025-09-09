<?php
// PublicationFactory.php
namespace Database\Factories;
use App\Models\Publication;
use Illuminate\Database\Eloquent\Factories\Factory;

class PublicationFactory extends Factory
{
    protected $model = Publication::class;
    
    public function definition(): array
    {
        return [
            'titulo' => $this->faker->sentence(4),
            'precio' => $this->faker->numberBetween(10000, 5000000),
            'descripcion' => $this->faker->paragraphs(2, true),
            'visibilidad' => $this->faker->boolean(85),
            // FK se asignarán en el seeder
        ];
    }
}