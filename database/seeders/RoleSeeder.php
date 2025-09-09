<?php
// RoleSeeder.php
namespace Database\Seeders;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Solo los 3 roles principales necesarios
        Role::create([
            'nombre' => 'Admin',
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
        
        // Solo algunos roles adicionales si es necesario
        Role::factory(7)->create();
    }
}