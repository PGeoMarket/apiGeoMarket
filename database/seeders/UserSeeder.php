<?php
// UserSeeder.php
namespace Database\Seeders;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('nombre', 'Admin')->first();
        $vendedorRole = Role::where('nombre', 'Vendedor')->first();
        $usuarioRole = Role::where('nombre', 'Usuario')->first();
        $otherRoles = Role::whereNotIn('nombre', ['Admin', 'Vendedor', 'Usuario'])->get();

        // Usuario administrador
        User::create([
            'primer_nombre' => 'Admin',
            'segundo_nombre' => null,
            'primer_apellido' => 'Sistema',
            'segundo_apellido' => null,
            'email' => 'admin@geomarket.com',
            'password_hash' => Hash::make('admin123'),
            'role_id' => $adminRole->id,
            'activo' => true,
        ]);

        // 30 usuarios vendedores
        User::factory(30)->create([
            'role_id' => $vendedorRole->id,
        ]);

        // 50 usuarios regulares
        User::factory(50)->create([
            'role_id' => $usuarioRole->id,
        ]);

        // 19 usuarios adicionales distribuidos entre los 3 roles principales
        $allRoles = Role::all(); // Solo los 3 roles
        for ($i = 0; $i < 19; $i++) {
            User::factory()->create([
                'role_id' => $allRoles->random()->id,
            ]);
        }
    }
}