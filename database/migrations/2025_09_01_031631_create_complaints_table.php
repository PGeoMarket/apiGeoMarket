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
            $table->id();
            $table->boolean('estado')->default(false);
            $table->text('descripcion_adicional')->nullable();
            $table->timestamps();

            // columnas para las claves foráneas
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('publication_id')->nullable();
            $table->unsignedBigInteger('reason_id')->nullable();

            // foránea de usuario
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');

            // foránea de publicación
            $table->foreign('publication_id')
                ->references('id')
                ->on('publications')
                ->onDelete('set null');

            // foránea de razón de queja
            $table->foreign('reason_id')
                ->references('id')
                ->on('reason_complaints')
                ->onDelete('set null');
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
