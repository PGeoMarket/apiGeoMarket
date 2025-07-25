<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    public function publications()  {
        return $this->belongsToMany(Publication::class);
    }

    public function rol()  {
        return $this->hasMany(role::class);
    }

    protected $fillable=['user_id'];
}
