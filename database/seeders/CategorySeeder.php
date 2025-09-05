<?php
namespace Database\Seeders;
use App\Models\Category;
use Illuminate\Database\Seeder;
class CategorySeeder extends Seeder
{
 public function run(): void
 {
 $categories = [
 'Electrónicos y Tecnología',
 'Ropa, Bolsas y Calzado',
 'Hogar, Muebles y Jardín',
 'Deportes y Fitness',
 'Automóviles y Motos',
 'Libros, Revistas y Comics',
 'Salud y Belleza',
 'Juguetes, Niños y Bebés',
 'Instrumentos Musicales',
 'Comida, Bebidas y Tabaco',
 'Animales y Mascotas',
 'Arte, Papelería y Manualidades',
 'Cámaras y Accesorios',
 'Celulares y Telefonía',
 'Inmuebles',
 'Industrias y Oficinas',
 'Joyas y Relojes',
 'Servicios',
 ];
 foreach ($categories as $category) {
 Category::create(['categoria' => $category]);
 }
 // Categorías adicionales con factory
 Category::factory(5)->create();
 }
}
