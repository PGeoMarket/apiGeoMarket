<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role()
{
    return $this->belongsTo(Role::class, 'id_rol');
}

public function comments()
{
    return $this->hasMany(Comment::class, 'user_id');
}

public function favoritePublications()
{
    return $this->belongsToMany(Publication::class, 'user_favorite');  
}

public function complaints()
{
    return $this->hasMany(Complaint::class, 'user_id');
}

public function chats()
{
    return $this->hasMany(Chat::class, 'user_id');
}

public function sentMessages()
{
    return $this->hasMany(Message::class, 'id_sender');
}

public function supportChats()
{
    return $this->hasMany(ChatSupport::class, 'user_id');
}

public function seller()
{
    return $this->hasOne(Seller::class, 'id_user');
}

}

