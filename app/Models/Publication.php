<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

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
    'max_distance',
    'categories'
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
        return $this->morphOne(Image::class, 'imageable');
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
                // ✅ Filtro por categoría(s) - soporta una o múltiples
                if ($filter === 'category_id') {
                    // Detectar si viene con comas (múltiples categorías)
                    if (is_string($value) && strpos($value, ',') !== false) {
                        $categoryIds = explode(',', $value);
                        $categoryIds = array_map('trim', $categoryIds); // Limpiar espacios
                        $query->whereIn('publications.category_id', $categoryIds);
                    }
                    // Array de categorías
                    elseif (is_array($value)) {
                        $query->whereIn('publications.category_id', $value);
                    }
                    // Categoría única
                    else {
                        $query->where('publications.category_id', $value);
                    }
                }
                // Filtro alternativo por múltiples categorías
                elseif ($filter === 'categories') {
                    $categoryIds = is_array($value) ? $value : explode(',', $value);
                    $categoryIds = array_map('trim', $categoryIds);
                    $query->whereIn('publications.category_id', $categoryIds);
                }
                // Filtro precio mínimo
                elseif ($filter === 'precio_min') {
                    $query->where('publications.precio', '>=', (float) $value);
                }
                // Filtro precio máximo
                elseif ($filter === 'precio_max') {
                    $query->where('publications.precio', '<=', (float) $value);
                }
                // Ignorar parámetros de ubicación (se procesan después)
                elseif (in_array($filter, ['user_lat', 'user_lon', 'max_distance'])) {
                    continue;
                }
                // Filtro genérico tipo LIKE
                else {
                    $query->where('publications.' . $filter, 'LIKE', '%'. $value . '%');
                }
            }
        }

        // Aplicar filtro de distancia si hay coordenadas
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
                    // ✅ Solo ordenar por distance
                    $query->orderBy('distance', $direction);
                }
            } else {
                // ✅ Especificar la tabla para evitar ambigüedad
                $query->orderBy('publications.' . $sortField, $direction);
            }
        }
    }
}
//
    public function scopeGetOrPaginate(Builder $query)
    {
        // ✅ AGREGAR: Distinct para evitar duplicados
        $query->distinct();

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
    $haversine = "(6371 * acos(cos(radians({$userLat}))
                  * cos(radians(coordinates.latitud))
                  * cos(radians(coordinates.longitud) - radians({$userLon}))
                  + sin(radians({$userLat}))
                  * sin(radians(coordinates.latitud))))";

    $sellerClass = 'App\\Models\\Seller';

    // Verificar si ya existe el join
    $hasJoin = collect($query->getQuery()->joins ?? [])->contains(function($join) {
        return strpos($join->table ?? '', 'coordinates') !== false;
    });

    if (!$hasJoin) {
        $query->join('coordinates', function($join) use ($sellerClass) {
            $join->on('publications.seller_id', '=', 'coordinates.coordinateable_id')
                 ->where('coordinates.coordinateable_type', '=', $sellerClass);
        });
    }

    // ✅ FORZAR el select completo
    $currentSelects = $query->getQuery()->columns;

    if (empty($currentSelects) || in_array('*', $currentSelects)) {
        // Si no hay select específico o es *, agregar todos los campos
        $query->select('publications.*', DB::raw("{$haversine} AS distance"));
    } else {
        // Si ya hay selects específicos, agregar distance
        $query->addSelect(DB::raw("{$haversine} AS distance"));
    }

    if ($maxDistance) {
        $query->having('distance', '<=', (float) $maxDistance);
    }

    return $query;
}

}
