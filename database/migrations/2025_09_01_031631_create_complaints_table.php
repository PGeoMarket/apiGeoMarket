<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
            // PRIMERO: Todas las columnas principales
            $table->id();
            $table->boolean('estado');
            $table->text('descripcion_adicional')->nullable();

            // SEGUNDO: Todas las columnas de claves foráneas
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('publication_id')->nullable();
            $table->unsignedBigInteger('reason_id')->nullable();

            // TERCERO: Timestamps
            $table->timestamps();

            // CUARTO: Al final, todas las relaciones foráneas
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('publication_id')->references('id')->on('publications')->onDelete('set null');
            $table->foreign('reason_id')->references('id')->on('reason_complaints')->onDelete('set null'); // Comentado
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
