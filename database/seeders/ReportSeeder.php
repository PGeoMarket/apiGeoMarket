<?php

namespace Database\Seeders;

use App\Models\Report;
use App\Models\User;
use App\Models\Publication;
use App\Models\ReasonComplaint;
use Illuminate\Database\Seeder;

class ReportSeeder extends Seeder
{
    public function run(): void
    {
        // Verificar que existan los datos necesarios
        $userCount = User::count();
        $publicationCount = Publication::count();
        $reasonCount = ReasonComplaint::count();

        if ($userCount === 0 || $publicationCount === 0 || $reasonCount === 0) {
            $this->command->warn('No se pueden crear reportes: faltan usuarios, publicaciones o razones de queja.');
            return;
        }

        // Crear algunos reportes de prueba
        $this->command->info('Creando reportes de publicaciones...');
        
        // Reportes para publicaciones (10 reportes)
        for ($i = 0; $i < 10; $i++) {
            $publication = Publication::inRandomOrder()->first();
            $reporter = User::where('id', '!=', $publication->seller->user_id)->inRandomOrder()->first();
            $reason = ReasonComplaint::where(function($q) {
                $q->where('applies_to', 'publication')->orWhere('applies_to', 'both');
            })->inRandomOrder()->first();

            if ($reporter && $reason) {
                // Verificar que no exista ya un reporte similar
                $existingReport = Report::where('user_id', $reporter->id)
                    ->where('reportable_type', 'publication')
                    ->where('reportable_id', $publication->id)
                    ->where('reason_id', $reason->id)
                    ->first();

                if (!$existingReport) {
                    Report::create([
                        'user_id' => $reporter->id,
                        'reportable_type' => Publication::class,
                        'reportable_id' => $publication->id,
                        'reason_id' => $reason->id,
                        'descripcion_adicional' => fake()->optional(0.6)->paragraph(),
                        'estado' => fake()->boolean(25), // 25% resueltos
                    ]);
                }
            }
        }

        $this->command->info('Creando reportes de usuarios...');
        
        // Reportes para usuarios (8 reportes)
        for ($i = 0; $i < 8; $i++) {
            $userToReport = User::inRandomOrder()->first();
            $reporter = User::where('id', '!=', $userToReport->id)->inRandomOrder()->first();
            $reason = ReasonComplaint::where(function($q) {
                $q->where('applies_to', 'user')->orWhere('applies_to', 'both');
            })->inRandomOrder()->first();

            if ($reporter && $reason) {
                // Verificar que no exista ya un reporte similar
                $existingReport = Report::where('user_id', $reporter->id)
                    ->where('reportable_type', 'user')
                    ->where('reportable_id', $userToReport->id)
                    ->where('reason_id', $reason->id)
                    ->first();

                if (!$existingReport) {
                    Report::create([
                        'user_id' => $reporter->id,
                        'reportable_type' => User::class,
                        'reportable_id' => $userToReport->id,
                        'reason_id' => $reason->id,
                        'descripcion_adicional' => fake()->optional(0.7)->paragraph(),
                        'estado' => fake()->boolean(15), // 15% resueltos
                    ]);
                }
            }
        }

        $this->command->info('Reportes creados exitosamente.');
    }
}