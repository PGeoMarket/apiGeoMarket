<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{

    protected $fillable = [
        'user_id',
        'nombre',
        'descripcion',
        'foto_portada',
        'latitud_tienda',
        'longitud_tienda',
        'direccion_tienda',
        'fecha_creacion',
        'activo'
    ];

    // Relación: Seller pertenece a un User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación: Seller tiene muchos Phone
    public function phones()
    {
        return $this->hasMany(Phone::class);
    }

    // Relación: Seller tiene muchas Publication
    public function publications()
    {
        return $this->hasMany(Publication::class);
    }
}
