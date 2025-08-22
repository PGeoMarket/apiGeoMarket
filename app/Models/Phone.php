<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phone extends Model
{
     use HasFactory;

    public function seller()  {
        return $this->belongsTo(Seller::class);
    }

    protected $fillable=['numero_telefono','seller_id'];
}
