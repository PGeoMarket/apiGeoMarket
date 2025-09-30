<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Chat extends Model
{
    protected $fillable = [
        'initiator_user_id',
        'responder_user_id',
        'publication_id',
        'ably_channel_id',
        'status'
    ];

    // Relaciones
    public function initiator()
    {
        return $this->belongsTo(User::class, 'initiator_user_id');
    }

    public function responder()
    {
        return $this->belongsTo(User::class, 'responder_user_id');
    }

    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class)->orderBy('sent_at', 'asc');
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class)->latestOfMany('sent_at');
    }

    // Auto-generar canal único para Ably
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($chat) {
            if (empty($chat->ably_channel_id)) {
                $chat->ably_channel_id = 'chat-' . Str::uuid();
            }
        });
    }

    // Métodos útiles
    public function isParticipant($userId)
    {
        return $this->initiator_user_id == $userId || $this->responder_user_id == $userId;
    }

    public function getOtherParticipant($currentUserId)
    {
        return $this->initiator_user_id == $currentUserId ? $this->responder : $this->initiator;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('initiator_user_id', $userId)
                    ->orWhere('responder_user_id', $userId);
    }
}
