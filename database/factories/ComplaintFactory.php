<?php
// ComplaintFactory.php
namespace Database\Factories;
use App\Models\Complaint;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComplaintFactory extends Factory
{
    protected $model = Complaint::class;
    
    public function definition(): array
    {
        return [
            'estado' => $this->faker->boolean(30),
            'descripcion_adicional' => $this->faker->text(200),
            // FK se asignarán en el seeder para mejor distribución
        ];
    }
}