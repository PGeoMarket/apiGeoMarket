<?php
namespace Database\Factories;

use App\Models\Complaint;
use App\Models\User;
use App\Models\Publication;
use App\Models\ReasonComplaint;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComplaintFactory extends Factory
{
    protected $model = Complaint::class;

    public function definition(): array
    {
        return [
            'estado' => $this->faker->boolean(30), // CORREGIDO: era Estado
            'descripcion_adicional' => $this->faker->paragraph(),
            'user_id' => User::factory(),
            'publication_id' => Publication::factory(),
            'reason_id' => ReasonComplaint::factory(),
        ];
    }
}