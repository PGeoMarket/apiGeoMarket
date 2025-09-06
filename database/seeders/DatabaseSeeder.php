<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,          // PRIMERO - requerido por users
            UserSeeder::class,
            SellerSeeder::class,
            PhoneSeeder::class,
            CategorySeeder::class,
            PublicationSeeder::class,
            CoordinateSeeder::class,    // NUEVO - después de users y sellers
            ImageSeeder::class,         // NUEVO - después de users, sellers y publications
            CommentSeeder::class,
            ReasonComplaintSeeder::class,
            ComplaintSeeder::class,
            PublicationUserSeeder::class, // Tabla pivote al final
        ]);
    }
}