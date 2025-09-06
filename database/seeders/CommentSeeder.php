<?php

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
        // Comentarios aleatorios en publicaciones
        foreach ($publications->take(50) as $publication) {
            Comment::factory(50)->create([
                'user_id' => $users->random()->id,
                'publication_id' => $publication->id,
            ]);
        }
        // Comentarios adicionales
        Comment::factory(200)->create([
            'user_id' => $users->random()->id,
            'publication_id' => $publications->random()->id,
        ]);
    }
}
