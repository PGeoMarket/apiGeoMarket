<?php
// ============================================================================
// FACTORIES CORREGIDOS
// ============================================================================

// CoordinateFactory.php - NUEVO
namespace Database\Factories;

use App\Models\Coordinate;
use App\Models\User;
use App\Models\Seller;
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

    public function forUser()
    {
        return $this->state(function (array $attributes) {
            return [
                'coordinateable_type' => User::class,
                'coordinateable_id' => User::factory(),
            ];
        });
    }

    public function forSeller()
    {
        return $this->state(function (array $attributes) {
            return [
                'coordinateable_type' => Seller::class,
                'coordinateable_id' => Seller::factory(),
            ];
        });
    }
}