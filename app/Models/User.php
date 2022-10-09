<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'firstName',
        'surName',
        'age',
        'genderId'       
    ];

    public function connections(){
        return $this->belongsToMany(
            User::class,
            'user_connections',
            'userId',
            'connectionUserId'
        );
    }

    public function gender(){
        return $this->hasOne(
            Gender::class,
            'id',
            'genderId'
        );
    }

    
    public function visits(){
        return $this->belongsToMany(
            City::class,
            'cities_visit',
            'userId',
            'cityId'
        )->withPivot('rate');
    }

    public function visitRate(){
        return $this->hasMany(
            CityVisit::class,
            'userId'
        );
    }
}
