<?php
// PublicationSeeder.php
namespace Database\Seeders;
use App\Models\Publication;
use App\Models\Seller;
use App\Models\Category;
use Illuminate\Database\Seeder;

class PublicationSeeder extends Seeder
{
    public function run(): void
    {
        $sellers = Seller::all();
        $categories = Category::all();

        if ($sellers->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('No hay sellers o categorías disponibles para crear publicaciones');
            return;
        }

        // Cada seller tiene entre 1-8 publicaciones
        foreach ($sellers as $seller) {
            $publicationCount = rand(1, 8);
            
            for ($i = 0; $i < $publicationCount; $i++) {
                Publication::factory()->create([
                    'seller_id' => $seller->id,
                    'category_id' => $categories->random()->id,
                ]);
            }
        }

        // Publicaciones adicionales con distribución aleatoria
        for ($i = 0; $i < 100; $i++) {
            Publication::factory()->create([
                'seller_id' => $sellers->random()->id,
                'category_id' => $categories->random()->id,
            ]);
        }
    }
}
