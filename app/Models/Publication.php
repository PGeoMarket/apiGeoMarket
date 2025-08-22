<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

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
        'category_id'
    ];

    //en la asignacion masiva supongo que no son necesarias las fechas

    protected $allowIncluded = [
        'seller',
        'seller.user',
        'category',
        'comments',
        'comments.user',
        'usersWhofavorited', //favorite puede cambiar el nombre del modelo a futuro
        'complaints',
        'complaints.reasoncomplaint',
        'chats',
        'image'
    ];

    protected $allowFilter = [
        'id',
        'titulo',
        'seller_id',
        'category_id',
        'precio'
    ];

    protected $allowSort = [
        'id',
        'titulo',
        'precio',
        'fecha_actualizacion' //filtrar por actualizacion o publicacion?
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

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }
    
    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }


    /*      public function chats() {
        return $this->hasMany(Chat::class);
    }
 */
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
