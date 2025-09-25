<?php
// CommentFactory.php
namespace Database\Factories;
use App\Models\Comment;
use App\Models\User;
use App\Models\Publication;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    protected $model = Comment::class;
    
    public function definition(): array
    {
        return [
            'texto' => $this->faker->paragraph(2),
            'valor_estrella' => $this->faker->numberBetween(1, 5),
            // No asignamos FK aquí, se harán en el seeder para mejor control
        ];
    }
}