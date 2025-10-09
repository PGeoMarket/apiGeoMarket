<?php

namespace Database\Factories;

use App\Models\ReasonComplaint;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReasonComplaintFactory extends Factory
{
    protected $model = ReasonComplaint::class;

    public function definition(): array
    {
        
        $publicationReasons = [
            'Producto prohibido',
            'Información engañosa',
            'Producto peligroso',
            'Práctica comercial sospechosa'
        ];

        $userReasons = [
            'Perfil falso',
            'Comportamiento abusivo',
            'Vendedor fraudulento'
        ];

        $bothReasons = [
            'Spam',
            'Contenido inapropiado',
            'Otro motivo'
        ];

        // Seleccionar tipo aleatoriamente
        $appliesTo = $this->faker->randomElement(['publication', 'user', 'both']);
        
        // Seleccionar motivo según el tipo
        switch ($appliesTo) {
            case 'publication':
                $motivo = $this->faker->randomElement($publicationReasons);
                break;
            case 'user':
                $motivo = $this->faker->randomElement($userReasons);
                break;
            case 'both':
                $motivo = $this->faker->randomElement($bothReasons);
                break;
            default:
                $motivo = $this->faker->randomElement($bothReasons);
        }

        return [
            'motivo' => $motivo,
            'applies_to' => $appliesTo,  // ❌ ESTO FALTABA EN TU CÓDIGO
        ];
    }
}