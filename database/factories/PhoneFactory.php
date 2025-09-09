<?php
// PhoneFactory.php
namespace Database\Factories;
use App\Models\Phone;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhoneFactory extends Factory
{
    protected $model = Phone::class;
    
    public function definition(): array
    {
        return [
            'numero_telefono' => $this->faker->numberBetween(3000000000, 3999999999),
            // seller_id se asignará en el seeder
        ];
    }
}