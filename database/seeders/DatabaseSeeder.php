<?php
// DatabaseSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            // 🔒 Solo estos dos seeders en producción
            $this->call([
                RoleSeeder::class,
                CategorySeeder::class,
            ]);
        } else {
            // 💻 Todo el conjunto en desarrollo o staging
            $this->call([
                RoleSeeder::class,
                CategorySeeder::class,
                ReasonComplaintSeeder::class,
                UserSeeder::class,
                SellerSeeder::class,
                PhoneSeeder::class,
                PublicationSeeder::class,
                CoordinateSeeder::class,
                ImageSeeder::class,
                CommentSeeder::class,
                ReportSeeder::class,
                PublicationUserSeeder::class,
            ]);
        }
    }
}
