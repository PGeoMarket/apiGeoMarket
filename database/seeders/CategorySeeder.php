<?php
// CategorySeeder.php
namespace Database\Seeders;
use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Electrodomésticos',
            'Alimentos',
            'Entretenimiento', 
            'Muebles'
        ];

        foreach ($categories as $category) {
            Category::create(['categoria' => $category]);
        }
    }
}