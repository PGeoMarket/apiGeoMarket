<?php
// CoordinateSeeder.php
namespace Database\Seeders;
use App\Models\Coordinate;
use App\Models\User;
use App\Models\Seller;
use Illuminate\Database\Seeder;

class CoordinateSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $sellers = Seller::all();

        // 95% de usuarios tienen coordenadas
        $usersWithCoords = $users->random(intval($users->count() * 0.95));
        foreach ($usersWithCoords as $user) {
            Coordinate::factory()->create([
                'coordinateable_type' => User::class,
                'coordinateable_id' => $user->id,
            ]);
        }

        // 95% de sellers tienen coordenadas
        $sellersWithCoords = $sellers->random(intval($sellers->count() * 0.95));
        foreach ($sellersWithCoords as $seller) {
            Coordinate::factory()->create([
                'coordinateable_type' => Seller::class,
                'coordinateable_id' => $seller->id,
            ]);
        }
    }
}