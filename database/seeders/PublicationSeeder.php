<?php
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
 // Cada seller tiene entre 1-10 publicaciones
 foreach ($sellers as $seller) {
 Publication::factory(rand(1, 10))->create([
 'seller_id' => $seller->id,
 'category_id' => $categories->random()->id,
 ]);
 }
 // Publicaciones adicionales
 Publication::factory(100)->create([
 'seller_id' => $sellers->random()->id,
 'category_id' => $categories->random()->id,
 ]);
 }
}