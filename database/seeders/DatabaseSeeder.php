<?php
// DatabaseSeeder.php
namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Primero los datos base (sin FK)
            RoleSeeder::class,
            CategorySeeder::class, 
            ReasonComplaintSeeder::class,
            
            // Luego usuarios (dependen de roles)
            UserSeeder::class,
            
            // Sellers (dependen de usuarios)
            SellerSeeder::class,
            PhoneSeeder::class,
            
            // Publicaciones (dependen de sellers y categorías)
            PublicationSeeder::class,
            
            // Datos que usan polimorfismo
            CoordinateSeeder::class,
            ImageSeeder::class,
            
            // Comentarios y quejas (dependen de users y publications)
            CommentSeeder::class,
            ReportSeeder::class,
            
            // Tabla pivote al final
            PublicationUserSeeder::class,
        ]);
    }
}