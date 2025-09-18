<?php

// MODELO - app/Models/ChatSupport.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatSupport extends Model
{
    protected $table = 'chats_support';
    
    protected $fillable = [
        'mensaje',
        'user_id',
        'fecha_mensaje'
    ];

    protected $casts = [
        'fecha_mensaje' => 'datetime'
    ];

    // Desactivar timestamps automáticos de Laravel
    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}