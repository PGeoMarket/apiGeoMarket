<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;


class User extends Authenticatable
{
    use HasFactory,HasApiTokens;

    // Relaciones que se pueden incluir
    protected $allowIncluded = [
        'role',
        'seller',
        'seller.phones',
        'seller.publications.image',
        'seller.coordinate',
        'seller.image',
        'comments',
        'reports',
        'favoritePublications',
        'favoritePublications.category',
        'favoritePublications.seller',
        'favoritePublications.image',
        'support',
        'image'
    ];

    // Campos por los que se puede filtrar
    protected $allowFilter = [
        'id',
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'email',
        'role_id',
        'activo',
        'created_at',
        'updated_at'
    ];

    // Campos por los que se puede ordenar
    protected $allowSort = [
        'id',
        'primer_nombre',
        'primer_apellido',
        'email',
        'role_id',
        'activo',
        'created_at',
        'updated_at'
    ];
    protected $fillable = [
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'email',
        'password_hash',
        'role_id',
        'activo'
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function favoritePublications()
    {
        return $this->belongsToMany(Publication::class);
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function seller()
    {
        return $this->hasOne(Seller::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function coordinate()
    {
        return $this->morphOne(Coordinate::class, 'coordinateable');
    }

    public function supports()
    {
        return $this->hasMany(Support::class);
    }

    public function messages(){
        return $this->hasMany(Message::class);
    }

    public function chats()  {
        return $this->hasMany(Chat::class);
    }

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
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
