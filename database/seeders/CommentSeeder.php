<?php
// CommentSeeder.php
namespace Database\Seeders;
use App\Models\Comment;
use App\Models\User;
use App\Models\Publication;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $publications = Publication::where('visibilidad', true)->get();

        if ($users->isEmpty() || $publications->isEmpty()) {
            $this->command->warn('No hay usuarios o publicaciones disponibles para crear comentarios');
            return;
        }

        // Crear 500 comentarios con distribución aleatoria
        for ($i = 0; $i < 500; $i++) {
            Comment::factory()->create([
                'user_id' => $users->random()->id,
                'publication_id' => $publications->random()->id,
            ]);
        }

        // Algunas publicaciones populares tienen muchos comentarios
        $popularPublications = $publications->random(10);
        foreach ($popularPublications as $publication) {
            $commentCount = rand(10, 25);
            
            for ($i = 0; $i < $commentCount; $i++) {
                Comment::factory()->create([
                    'user_id' => $users->random()->id,
                    'publication_id' => $publication->id,
                ]);
            }
        }
    }
}