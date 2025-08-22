<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::all();

        // Usuario administrador
        User::create([
            'primer_nombre' => 'Admin',
            'segundo_nombre' => null,
            'primer_apellido' => 'Sistema',
            'segundo_apellido' => null,
            'foto' => null,
            'email' => 'admin@geomarket.com',
            'password_hash' => Hash::make('admin123'),
            'rol_id' => $roles->where('nombre', 'Super Admin')->first()->id,
            'latitud' => 4.7110,
            'longitud' => -74.0721,
            'direccion_completa' => 'Bogotá, Colombia',
            'activo' => true,
        ]);
        // Usuarios regulares
        User::factory(50)->create([
            'rol_id' => $roles->random()->id,
        ]);
    }
}
