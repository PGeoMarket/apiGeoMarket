<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\User;
use App\Models\Seller;
use App\Models\Publication;
use Illuminate\Database\Seeder;

class ImageSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $sellers = Seller::all();
        $publications = Publication::all();

        // Imágenes de perfil para usuarios (60% de los usuarios)
        foreach ($users->random($users->count() * 0.6) as $user) {
            Image::factory()->forUser()->create([
                'imageable_id' => $user->id,
            ]);
        }

        // Imágenes de portada para sellers (70% de los sellers)
        foreach ($sellers->random($sellers->count() * 0.7) as $seller) {
            Image::factory()->forSeller()->create([
                'imageable_id' => $seller->id,
            ]);
        }

        // Imágenes para publicaciones (1-5 imágenes por publicación)
        foreach ($publications as $publication) {
            $imageCount = rand(1, 5);
            for ($i = 0; $i < $imageCount; $i++) {
                Image::factory()->forPublication()->create([
                    'imageable_id' => $publication->id,
                ]);
            }
        }
    }
}
