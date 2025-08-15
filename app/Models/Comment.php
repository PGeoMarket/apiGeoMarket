<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{

    protected $fillable = ['texto', 'valor_estrella', 'user_id', 'publication_id'];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function publication()
    {
        return $this->belongsTo(Publication::class, 'publication_id');
    }

    public function scopeByUser($query, $userId)
{
    return $query->where('user_id', $userId);
}

// Filtrar por publicación
public function scopeByPublication($query, $publicationId)
{
    return $query->where('publication_id', $publicationId);
}

// Filtrar por cantidad exacta de estrellas
public function scopeByStars($query, $stars)
{
    return $query->where('valor_estrella', $stars);
}

// Buscar texto dentro del comentario
public function scopeSearch($query, $keyword)
{
    return $query->where('texto', 'LIKE', "%{$keyword}%");
}
}
