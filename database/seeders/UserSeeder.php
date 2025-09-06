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
            'email' => 'admin@geomarket.com',
            'password_hash' => Hash::make('admin123'),
            'role_id' => $roles->where('nombre', 'Super Admin')->first()->id, // CORREGIDO
            'activo' => true,
        ]);

        // Usuarios regulares
        User::factory(100)->create([
            'role_id' => $roles->random()->id, // CORREGIDO
        ]);
    }
}