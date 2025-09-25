<?php
// PublicationUserSeeder.php
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

        if ($users->isEmpty() || $publications->isEmpty()) {
            $this->command->warn('No hay usuarios o publicaciones para crear favoritos');
            return;
        }

        // 80% de usuarios tienen publicaciones favoritas
        $usersWithFavorites = $users->random(intval($users->count() * 0.8));
        
        foreach ($usersWithFavorites as $user) {
            // Cada usuario marca entre 1-12 publicaciones como favoritas
            $favoriteCount = rand(1, 15);
            $randomPublications = $publications->random($favoriteCount);
            
            // Usar syncWithoutDetaching para evitar duplicados
            $user->favoritePublications()->syncWithoutDetaching($randomPublications->pluck('id'));
        }

        // Algunos usuarios super activos con muchos favoritos
        $superActiveUsers = $users->random(8);
        foreach ($superActiveUsers as $user) {
            $favoriteCount = rand(15, 30);
            $randomPublications = $publications->random($favoriteCount);
            
            $user->favoritePublications()->syncWithoutDetaching($randomPublications->pluck('id'));
        }
    }
}