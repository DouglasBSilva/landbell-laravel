<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CityVisit extends Model
{
    use HasFactory;

    protected $table = 'cities_visit';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cityId',
        'userId',
        'rate'       
    ];
}
