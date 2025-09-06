<?php

namespace Database\Seeders;

use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Seeder;

class SellerSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::whereHas('role', function ($query) {
            $query->where('nombre', 'Vendedor');
        })->get();
        // Si no hay usuarios vendedores, crear algunos
        if ($users->isEmpty()) {
            $users = User::factory(15)->create([
                'role_id' => \App\Models\Role::where('nombre', 'Vendedor')->first()->id,
            ]);
        }
        // Crear sellers para usuarios vendedores
        foreach ($users->take(15) as $user) {
            Seller::factory()->create([
                'user_id' => $user->id,
            ]);
        }
        // Sellers adicionales
        Seller::factory(50)->create();
    }
}
