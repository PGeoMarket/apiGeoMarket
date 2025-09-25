<?php

namespace Database\Seeders;

use App\Models\ReasonComplaint;
use Illuminate\Database\Seeder;

class ReasonComplaintSeeder extends Seeder
{
    public function run(): void
    {
        
        
        $reasons = [
            // Razones específicas para publicaciones
            [
                'motivo' => 'Producto prohibido',
                'applies_to' => 'publication'
            ],
            [
                'motivo' => 'Información engañosa',
                'applies_to' => 'publication'  
            ],
            [
                'motivo' => 'Producto peligroso',
                'applies_to' => 'publication'
            ],
            [
                'motivo' => 'Práctica comercial sospechosa',
                'applies_to' => 'publication'
            ],
            
            // Razones específicas para usuarios
            [
                'motivo' => 'Perfil falso o suplantación',
                'applies_to' => 'user'
            ],
            [
                'motivo' => 'Comportamiento abusivo',
                'applies_to' => 'user'
            ],
            
            // Razones que aplican a ambos
            [
                'motivo' => 'Spam',
                'applies_to' => 'both'
            ],
            [
                'motivo' => 'Contenido inapropiado',
                'applies_to' => 'both'
            ],
            [
                'motivo' => 'Otro motivo',
                'applies_to' => 'both'
            ]
        ];

        foreach ($reasons as $reason) {
            ReasonComplaint::create($reason);  // ✅ Ahora incluye ambos campos
        }
    }
}