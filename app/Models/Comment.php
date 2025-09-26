<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends Model
{

    use HasFactory;
    protected $fillable = [
        'texto',
        'valor_estrella',
        'user_id',
        'publication_id'
    ];
    protected $allowIncluded = [
        'user',
        'user.image',
        'user.role',
        'user.seller',
        'publication',
        'publication.seller',
        'publication.seller.user',
        'publication.image',
        'publication.category'
    ];

    // Campos por los que se puede filtrar
    protected $allowFilter = [
        'id',
        'texto',
        'valor_estrella',
        'user_id',
        'publication_id',
        'created_at',
        'updated_at'
    ];

    // Campos por los que se puede ordenar
    protected $allowSort = [
        'id',
        'valor_estrella',
        'user_id',
        'publication_id',
        'created_at',
        'updated_at'
    ];


    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function publication()
    {
        return $this->belongsTo(Publication::class, 'publication_id');
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

                if (in_array($filter, ['id', 'user_id', 'publication_id', 'valor_estrella'])) {
                    $query->where($filter, $value); // comparación exacta
                } else {
                    $query->where($filter, 'LIKE', '%' . $value . '%'); // búsqueda parcial
                }
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

            //return $sortField;

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
