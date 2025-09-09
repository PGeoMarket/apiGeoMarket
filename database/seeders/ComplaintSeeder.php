<?php
// ComplaintSeeder.php
namespace Database\Seeders;
use App\Models\Complaint;
use App\Models\User;
use App\Models\Publication;
use App\Models\ReasonComplaint;
use Illuminate\Database\Seeder;

class ComplaintSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $publications = Publication::all();
        $reasons = ReasonComplaint::all();

        if ($users->isEmpty() || $publications->isEmpty() || $reasons->isEmpty()) {
            $this->command->warn('Faltan datos necesarios para crear complaints');
            return;
        }

        // Crear 80 quejas con distribución equilibrada de motivos
        for ($i = 0; $i < 80; $i++) {
            Complaint::factory()->create([
                'user_id' => $users->random()->id,
                'publication_id' => $publications->random()->id,
                'reason_id' => $reasons->random()->id,
            ]);
        }
    }
}