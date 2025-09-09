<?php
// ReasonComplaintFactory.php
namespace Database\Factories;
use App\Models\ReasonComplaint;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReasonComplaintFactory extends Factory
{
    protected $model = ReasonComplaint::class;
    
    public function definition(): array
    {
        return [
            'motivo' => $this->faker->randomElement([
                'Producto prohibido',
                'Información engañosa', 
                'Spam',
                'Contenido inapropiado',
                'Producto peligroso',
                'Práctica comercial sospechosa',
                'Otro motivo'
            ]),
        ];
    }
}