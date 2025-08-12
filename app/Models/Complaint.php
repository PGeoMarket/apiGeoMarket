<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Complaint extends Model
{
    // si tu columna en la migración es "Estado" (mayúscula), lo dejamos así
    protected $fillable = [
        'Estado',
        'descripcion_adicional',
        'user_id',
        'publication_id',
        'reason_id',
    ];

    protected $casts = [
        'Estado' => 'boolean',
    ];

    protected $allowIncluded = [
        'user',
        'publication',
        'reason'
    ];

    protected $allowFilter = [
        'id',
        'descripcion_adicional',
        'user_id',
        'publication_id',
        'reason_id'
    ];

    protected $allowSort = [
        'id',
        'descripcion_adicional',
        'created_at',
        'updated_at'
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    // relación 'reason' para que el controlador pueda usar load('reason')
    public function reason()
    {
        return $this->belongsTo(ReasonComplaint::class, 'reason_id');
    }

    // ===== Scopes (copiados/ajustados) =====
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
