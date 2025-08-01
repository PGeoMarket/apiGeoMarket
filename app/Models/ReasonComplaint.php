<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReasonComplaint extends Model
{
    protected $fillable = [
        'motivo'
    ];

    //Relaciones
    public function complaints() {
        return $this->belongsToMany(Complaint::class);
    }


}
