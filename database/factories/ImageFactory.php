<?php

namespace Database\Factories;

use App\Models\Image;
use App\Models\User;
use App\Models\Seller;
use App\Models\Publication;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImageFactory extends Factory
{
    protected $model = Image::class;

    public function definition(): array
    {
        return [
            'url' => $this->faker->imageUrl(640, 480, 'products'),
        ];
    }

    public function forUser()
    {
        return $this->state(function (array $attributes) {
            return [
                'imageable_type' => User::class,
                'imageable_id' => User::factory(),
            ];
        });
    }

    public function forSeller()
    {
        return $this->state(function (array $attributes) {
            return [
                'imageable_type' => Seller::class,
                'imageable_id' => Seller::factory(),
            ];
        });
    }

    public function forPublication()
    {
        return $this->state(function (array $attributes) {
            return [
                'imageable_type' => Publication::class,
                'imageable_id' => Publication::factory(),
            ];
        });
    }
}