<?php

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
        // Generar algunas quejas
        Complaint::factory(50)->create([
            'user_id' => $users->random()->id,
            'publication_id' => $publications->random()->id,
            'reason_id' => $reasons->random()->id,
        ]);
    }
}
