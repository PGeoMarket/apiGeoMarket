<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReasonComplaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'motivo'
    ];

    //Relaciones
    public function complaints() {
        return $this->hasMany(Complaint::class);
    }


}
