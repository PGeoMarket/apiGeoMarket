<?php

namespace Database\Seeders;

use App\Models\ReasonComplaint;
use Illuminate\Database\Seeder;

class ReasonComplaintSeeder extends Seeder
{
    public function run(): void
    {
        $reasons = [
            'Contenido inapropiado o ofensivo',
            'Información falsa o engañosa',
            'Spam o contenido repetitivo',
            'Posible fraude o estafa',
            'Precio incorrecto o sospechoso',
            'Producto no disponible',
            'Violación de términos de servicio',
            'Publicación duplicada',
            'Imágenes inapropiadas',
            'Contacto sospechoso del vendedor',
        ];
        foreach ($reasons as $reason) {
            ReasonComplaint::create(['razon' => $reason]);
        }
    }
}
