<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
        'user_id', 'reportable_id', 'reportable_type', 
        'reason_id', 'descripcion_adicional', 'estado'
    ];

    protected $allowIncluded = [
        'reporter',
        'reporter.role',
        'reporter.image',
        'reason',
        'reportable'
    ];

    protected $allowFilter = [
        'id',
        'user_id',
        'reportable_type',
        'reportable_id',
        'reason_id',
        'estado',
        'created_at',
        'updated_at'
    ];

    protected $allowSort = [
        'id',
        'user_id',
        'reportable_type',
        'reportable_id',
        'reason_id',
        'estado',
        'created_at',
        'updated_at'
    ];


    // Relación polimórfica
    public function reportable()
    {
        return $this->morphTo();
    }

    // Quien reporta
    public function reporter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Razón del reporte
    public function reason()
    {
        return $this->belongsTo(ReasonComplaint::class, 'reason_id');
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
                $query->WHERE($filter, 'LIKE', '%' . $value . '%');
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
    
}