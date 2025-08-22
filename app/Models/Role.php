<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    
    public function users()
    {
        return $this->hasMany(User::class);
    }

    protected $fillable=['nombre','permisos'];
}
