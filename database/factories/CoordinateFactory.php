<?php
// CoordinateFactory.php
namespace Database\Factories;
use App\Models\Coordinate;
use Illuminate\Database\Eloquent\Factories\Factory;

class CoordinateFactory extends Factory
{
    protected $model = Coordinate::class;
    
    public function definition(): array
    {
        return [
            'latitud' => $this->faker->latitude(1.0, 12.0), // Colombia coords
            'longitud' => $this->faker->longitude(-82.0, -66.0), // Colombia coords
            'direccion' => $this->faker->address(),
        ];
    }
}