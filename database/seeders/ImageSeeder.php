<?php
// ImageSeeder.php
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

        // 60% de usuarios tienen imagen de perfil
        $usersWithImages = $users->random(intval($users->count() * 0.6));
        foreach ($usersWithImages as $user) {
            Image::factory()->create([
                'imageable_type' => User::class,
                'imageable_id' => $user->id,
            ]);
        }

        // 70% de sellers tienen imagen de portada
        $sellersWithImages = $sellers->random(intval($sellers->count() * 0.7));
        foreach ($sellersWithImages as $seller) {
            Image::factory()->create([
                'imageable_type' => Seller::class,
                'imageable_id' => $seller->id,
            ]);
        }

        // Cada publicación tiene entre 1-4 imágenes
        foreach ($publications as $publication) {
            $imageCount = rand(1, 4);
            
            for ($i = 0; $i < $imageCount; $i++) {
                Image::factory()->create([
                    'imageable_type' => Publication::class,
                    'imageable_id' => $publication->id,
                ]);
            }
        }
    }
}