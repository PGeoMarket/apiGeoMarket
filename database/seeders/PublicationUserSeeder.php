<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Publication;
use Illuminate\Database\Seeder;

class PublicationUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $publications = Publication::where('visibilidad', true)->get();
        // Cada usuario tiene publicaciones favoritas/guardadas
        foreach ($users->take(30) as $user) {
            $randomPublications = $publications->random(rand(1, 8));
            $user->favoritePublications()->attach($randomPublications->pluck('id'));
        }
        // Algunos usuarios tienen muchas publicaciones guardadas
        foreach ($users->random(10) as $user) {
            $randomPublications = $publications->random(rand(10, 25));
            $user->favoritePublications()->syncWithoutDetaching($randomPublications->pluck('id'));
        }
    }
}
