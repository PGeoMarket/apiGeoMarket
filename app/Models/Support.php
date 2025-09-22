<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Support extends Model
{
    protected $table = 'supports';
    
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

    public function user(){
        return $this->belongsTo(User::class);
    }
}
