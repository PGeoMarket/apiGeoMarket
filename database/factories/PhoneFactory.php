<?php
namespace Database\Factories;
use App\Models\Phone;
use App\Models\Seller;
use Illuminate\Database\Eloquent\Factories\Factory;
class PhoneFactory extends Factory
{
 protected $model = Phone::class;
 public function definition(): array
 {
 return [
 'numero_telefono' => $this->faker->numberBetween(3000000000, 3999999999), //
Colombian mobile format
 'seller_id' => Seller::factory(),
 ];
 }
}