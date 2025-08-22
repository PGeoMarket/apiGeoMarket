<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            SellerSeeder::class,
            PhoneSeeder::class,
            CategorySeeder::class,
            PublicationSeeder::class,
            CommentSeeder::class,
            ReasonComplaintSeeder::class,
            ComplaintSeeder::class,
            PublicationUserSeeder::class, // Tabla pivote al final
        ]);
    }
}
