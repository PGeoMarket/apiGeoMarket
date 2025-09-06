<?php
namespace Database\Factories;

use App\Models\ReasonComplaint;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReasonComplaintFactory extends Factory
{
    protected $model = ReasonComplaint::class;

    public function definition(): array
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

        return [
            'motivo' => $this->faker->randomElement($reasons),
        ];
    }
}