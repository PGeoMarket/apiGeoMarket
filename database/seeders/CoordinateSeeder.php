<?php

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

        // Coordenadas para usuarios (80% de los usuarios)
        foreach ($users->random($users->count() * 0.8) as $user) {
            Coordinate::factory()->forUser()->create([
                'coordinateable_id' => $user->id,
            ]);
        }

        // Coordenadas para sellers (90% de los sellers)
        foreach ($sellers->random($sellers->count() * 0.9) as $seller) {
            Coordinate::factory()->forSeller()->create([
                'coordinateable_id' => $seller->id,
            ]);
        }
    }
}

