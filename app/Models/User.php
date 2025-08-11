<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class User extends Model
{
    protected $fillable = [
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'foto',
        'email',
        'password_hash',
        'rol_id',
        'latitud',
        'longitud',
        'direccion_completa',
        'activo'
    ];

    protected $hidden = [
        'password_hash'
    ];

    // Relaciones permitidas en "included"
    protected $allowIncluded = [
        'rol',
        'favoritePublications'
    ];

    // Campos permitidos en "filter"
    protected $allowFilter = [
        'id',
        'primer_nombre',
        'primer_apellido',
        'email',
        'rol_id',
        'activo'
    ];

    // Campos permitidos en "sort"
    protected $allowSort = [
        'id',
        'primer_nombre',
        'primer_apellido',
        'email',
        'created_at',
        'updated_at'
    ];

 
    public function rol()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }

    public function favoritePublications()
    {
        return $this->belongsToMany(
            Publication::class,
            'favorites',       // Tabla pivote
            'user_id',         // FK usuario
            'publication_id'   // FK publicación
        );
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
