<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Publication extends Model
{
    use HasFactory;
    //
    protected $fillable = [
        'titulo',
        'precio',
        'descripcion',
        'visibilidad',
        'seller_id',
        'category_id',
        'puntuacion_promedio'
    ];
  // Relaciones que se pueden incluir
protected $allowIncluded = [
    'seller',
    'seller.user',
    'seller.user.role',
    'seller.phones',
    'seller.coordinate',
    'seller.image',
    'category',
    'comments',
    'comments.user',
    'comments.user.role',
    'reports',
    'reports.user',
    'reports.reason',
    'image',
    'usersWhoFavorited',
    'usersWhoFavorited.role'
];

// Campos por los que se puede filtrar
protected $allowFilter = [
    'id',
    'titulo',
    'precio',
    'descripcion',
    'visibilidad',
    'seller_id',
    'category_id',
    'created_at',
    'updated_at',
    'puntuacion_promedio',
    'precio_min',
    'precio_max',
    'user_lat',
    'user_lon',        
    'max_distance'
];

// Campos por los que se puede ordenar
protected $allowSort = [
    'id',
    'titulo',
    'precio',
    'visibilidad',
    'seller_id',
    'category_id',
    'created_at',
    'updated_at',
    'puntuacion_promedio',
    'distance'
];

    //Relaciones
    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function usersWhoFavorited()
    {
        return $this->belongsToMany(User::class);
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }
    
    public function image()
{
    return $this->morphTo('imageable', 'imageable_type', 'imageable_id');
}


    public function chats() {
        return $this->hasMany(Chat::class);
    }

    //Scopes
    public function scopeIncluded(Builder $query)
    {
        if (empty($this->allowIncluded) || empty(request("included"))) {
            return;
        }

        $relations = explode(',', request('included'));

        $allowIncluded = collect($this->allowIncluded);

        foreach ($relations as $key => $relationship) {

            if (!$allowIncluded->contains($relationship)) {
                unset($relations[$key]);
            }
        }

        $query->with($relations);
    }

    public function scopeFilter(Builder $query)
{
    if (empty($this->allowFilter) || empty(request("filter"))) {
        return;
    }

    $filters = request('filter');
    $allowFilter = collect($this->allowFilter);

    foreach ($filters as $filter => $value) {
        if ($allowFilter->contains($filter)) {
            if ($filter === 'precio_min') {
                $query->where('precio', '>=', (float) $value);
            } elseif ($filter === 'precio_max') {
                $query->where('precio', '<=', (float) $value);
            } elseif (in_array($filter, ['user_lat', 'user_lon', 'max_distance'])) {
                continue; // Se manejan después
            } else {
                $query->where($filter, 'LIKE', '%'. $value . '%');
            }
        }
    }

    // Aplicar filtro de distancia UNA SOLA VEZ
    $userLat = $filters['user_lat'] ?? null;
    $userLon = $filters['user_lon'] ?? null;
    
    if ($userLat && $userLon) {
        $this->applyDistanceCalculation($query, null, $filters['max_distance'] ?? null);
    }
    }


    public function scopeSort(Builder $query)
{
    if (empty($this->allowSort) || empty(request("sort"))) {
        return;
    }

    $sortFields = explode(',', request('sort'));
    $allowSort = collect($this->allowSort);

    foreach ($sortFields as $sortField) {
        $direction = 'asc';
        if (substr($sortField, 0, 1) === "-") {
            $direction = 'desc';
            $sortField = substr($sortField, 1);
        }

        if ($allowSort->contains($sortField)) {
            if ($sortField === 'distance') {
                // Verificar si ya se aplicó el cálculo de distancia
                $filters = request('filter');
                $userLat = $filters['user_lat'] ?? null;
                $userLon = $filters['user_lon'] ?? null;
                
                if ($userLat && $userLon) {
                    // Solo ordenar, no volver a aplicar joins
                    $query->orderBy('distance', $direction);
                }
            } else {
                $query->orderBy($sortField, $direction);
            }
        }
    }
    }
//
    public function scopeGetOrPaginate(Builder $query)
    {

        if (request('perPage')) {
            $perPage = intval(request('perPage'));

            if ($perPage) {
                return $query->paginate($perPage);
            }
        }

        return $query->get();
    }

    /**
     * Aplicar filtro de distancia (SOLO si hay coordenadas en el request)
     */
    private function applyDistanceFilter(Builder $query)
    {
        $filters = request('filter');
        
        $userLat = $filters['user_lat'] ?? null;
        $userLon = $filters['user_lon'] ?? null;
        $maxDistance = $filters['max_distance'] ?? null;

        // Si no hay coordenadas, no hacer nada
        if (!$userLat || !$userLon) {
            return;
        }

        // Aplicar el cálculo de distancia
        $this->applyDistanceCalculation($query, null, $maxDistance);
    }

    /**
     * Aplicar cálculo de distancia (Fórmula Haversine)
     */
    private function applyDistanceCalculation(Builder $query, $direction = null, $maxDistance = null)
{
    $filters = request('filter');
    
    $userLat = $filters['user_lat'] ?? null;
    $userLon = $filters['user_lon'] ?? null;

    if (!$userLat || !$userLon) {
        return $query;
    }

    // Fórmula Haversine
    $haversine = "(6371 * acos(cos(radians(?)) 
                  * cos(radians(coordinates.latitud)) 
                  * cos(radians(coordinates.longitud) - radians(?)) 
                  + sin(radians(?)) 
                  * sin(radians(coordinates.latitud))))";

    $sellerClass = 'App\\Models\\Seller';
    
    $query->join('coordinates', function($join) use ($sellerClass) {
            $join->on('publications.seller_id', '=', 'coordinates.coordinateable_id')
                 ->where('coordinates.coordinateable_type', '=', $sellerClass);
        })
        ->select('publications.*')
        ->selectRaw("{$haversine} AS distance", [$userLat, $userLon, $userLat]);

    if ($maxDistance) {
        $query->having('distance', '<=', (float) $maxDistance);
    }

    return $query;
    }
        
}
