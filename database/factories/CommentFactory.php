<?php
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
 'valor_estrella' => $this->faker->optional(0.8)->numberBetween(1, 5),
 'user_id' => User::factory(),
 'publication_id' => Publication::factory(),
 ];
 }
}