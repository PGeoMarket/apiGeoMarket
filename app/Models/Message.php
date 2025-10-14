<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'chat_id',
        'sender_id',
        'text',
        'message_type',
        'sent_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime'
    ];

    // Relaciones
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // Auto-set sent_at si no se proporciona
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($message) {
            if (empty($message->sent_at)) {
                $message->sent_at = now();
            }
        });
    }
}
