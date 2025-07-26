<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    public function Rol(){
        return $this->belongsTo('App\Models\Rol');
    }
    public function Publication(){
        return $this->belongsTo('App\Models\Publication');
    }
}
