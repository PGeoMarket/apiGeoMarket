<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReasonComplaint extends Model
{

    use HasFactory;
    
    protected $allowIncluded = [
        'reports',
        'reports.reporter',
        'reports.reportable'
    ];

    // Campos por los que se puede filtrar
    protected $allowFilter = [
        'id',
        'motivo',
        'created_at',
        'updated_at',
        'applies_to'
    ];

    // Campos por los que se puede ordenar
    protected $allowSort = [
        'id',
        'motivo',
        'created_at',
        'updated_at',
        'applies_to'
    ];
    protected $fillable = [
        'motivo',
        'applies_to'
    ];

    // Relaciones
    public function reports()
    {
        return $this->hasMany(Report::class, 'reason_id');
    }

    /**
     * ✅ MÉTODO FALTANTE - CRÍTICO PARA EL FUNCIONAMIENTO
     * Verifica si esta razón aplica para el tipo de contenido dado
     */
    public function appliesTo(string $type): bool
    {
        return $this->applies_to === 'both' || $this->applies_to === $type;
    }

    // Scope: included
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
