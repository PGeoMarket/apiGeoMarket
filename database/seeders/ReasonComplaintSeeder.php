<?php
// ReasonComplaintSeeder.php
namespace Database\Seeders;
use App\Models\ReasonComplaint;
use Illuminate\Database\Seeder;

class ReasonComplaintSeeder extends Seeder
{
    public function run(): void
    {
        $reasons = [
            'Producto prohibido',
            'Información engañosa',
            'Spam', 
            'Contenido inapropiado',
            'Producto peligroso',
            'Práctica comercial sospechosa',
            'Otro motivo'
        ];

        foreach ($reasons as $reason) {
            ReasonComplaint::create(['motivo' => $reason]);
        }
    }
}