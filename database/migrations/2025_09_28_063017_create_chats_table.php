<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            
            // Usuario que inicia el chat
            $table->foreignId('initiator_user_id')->constrained('users')->onDelete('cascade');
            
            // Usuario dueño de la publicación (responde)
            $table->foreignId('responder_user_id')->constrained('users')->onDelete('cascade');
            
            // Publicación sobre la que chatean
            $table->foreignId('publication_id')->constrained('publications')->onDelete('cascade');
            
            // Canal único para Ably
            $table->string('ably_channel_id', 255)->unique();
            
            // Estado del chat
            $table->string('status', 20)->default('active'); // active, closed
            
            $table->timestamps();
            
            // Constraint: un usuario solo puede iniciar un chat por publicación
            $table->unique(['initiator_user_id', 'publication_id'], 'unique_user_publication_chat');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chats');
    }
};
