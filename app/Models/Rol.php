<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    public function comments()
    {
        return $this->hasMany('App\Models\Coment');
    }

    public function sellers()
    {
        return $this->hasMany('App\Models\Seller');
    }

    public function faqs()
    {
        return $this->hasMany('App\Models\Faq');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function favorites()
    {
        return $this->hasMany('App\Models\Favorite');
    }
}
