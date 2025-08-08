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
            $table->boolean('Estado');
            $table->String('descripcion_adicional');
            $table->timestamps();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('publication_id')->nullable();
            $table->unsignedBigInteger('reason_id')->nullable();

            //foranea de usuario
            $table->foreign('user_id')
                ->references('id')
                ->on('users')->onDelete('set null');

            //foranea de publication
            $table->foreign('publication_id')
                ->references('id')
                ->on('publications')->onDelete('set null');

            //foranea de ReasonComplaint
            $table->foreign('reason_id')
                ->references('id')
                ->on('reason_complaints')->onDelete('set null');
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
