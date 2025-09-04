<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Seller extends Model
{

    use HasFactory;

    protected $fillable = [
        'user_id',
        'nombre_tienda',      // Corregido para coincidir con la migración
        'descripcion',
        'activo'
    ];

    // Listas blancas
    protected $allowIncluded = [
        'user',
        'phones',
        'publications',
        'image',
        'coordinate'
    ];

    protected $allowFilter = [
        'nombre_tienda',
        'activo',
        'user_id'
    ];

    protected $allowSort = [
        'id',
        'nombre_tienda',
        'activo',
        'created_at'
    ];

    // Relación: Seller pertenece a un User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación: Seller tiene muchos Phone
    public function phones()
    {
        return $this->hasMany(Phone::class);
    }

    // Relación: Seller tiene muchas Publication
    public function publications()
    {
        return $this->hasMany(Publication::class);
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function coordinate()
    {
        return $this->morphOne(Coordinate::class, 'coordinateable');
    }


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
                $query->where($filter, 'LIKE', '%' . $value . '%');
            }
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
                $query->orderBy($sortField, $direction);
            }
        }
    }

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
}
