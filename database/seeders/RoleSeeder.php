<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Roles predefinidos
        Role::create([
            'nombre' => 'Super Admin',
            'permisos' => json_encode([
                'read' => true,
                'write' => true,
                'delete' => true,
                'admin' => true,
                'manage_users' => true,
                'manage_roles' => true,
            ]),
        ]);
        Role::create([
            'nombre' => 'Vendedor',
            'permisos' => json_encode([
                'read' => true,
                'write' => true,
                'delete' => false,
                'admin' => false,
                'manage_publications' => true,
                'manage_shop' => true,
            ]),
        ]);
        Role::create([
            'nombre' => 'Usuario',
            'permisos' => json_encode([
                'read' => true,
                'write' => false,
                'delete' => false,
                'admin' => false,
                'comment' => true,
                'favorite' => true,
            ]),
        ]);
        // Roles adicionales con factory
        Role::factory(5)->create();
    }
}
