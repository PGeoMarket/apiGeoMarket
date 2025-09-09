<?php
// SellerSeeder.php
namespace Database\Seeders;
use App\Models\Seller;
use App\Models\User;
use Illuminate\Database\Seeder;

class SellerSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener todos los usuarios con rol vendedor
        $vendedores = User::whereHas('role', function ($query) {
            $query->where('nombre', 'Vendedor');
        })->get();

        // Crear un seller para cada usuario vendedor
        foreach ($vendedores as $vendedor) {
            Seller::factory()->create([
                'user_id' => $vendedor->id,
            ]);
        }

        // Algunos usuarios adicionales también pueden ser sellers ocasionalmente
        $otrosUsuarios = User::whereHas('role', function ($query) {
            $query->where('nombre', 'Usuario');
        })->inRandomOrder()->take(5)->get();

        foreach ($otrosUsuarios as $usuario) {
            Seller::factory()->create([
                'user_id' => $usuario->id,
            ]);
        }
    }
}