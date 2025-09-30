<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            
            // Relación con el chat
            $table->foreignId('chat_id')->constrained('chats')->onDelete('cascade');
            
            // Usuario que envía el mensaje
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            
            // Contenido del mensaje
            $table->text('text');
            
            // Tipo de mensaje
            $table->string('message_type', 20)->default('text');
            
            // Cuándo se envió
            $table->timestamp('sent_at')->useCurrent();
            
            $table->timestamps();
            
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
};
