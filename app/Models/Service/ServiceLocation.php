<?php

namespace App\Models\Service;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country_id',
        'timezone',
        'active',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function promos()
    {
        return $this->hasMany(Promo::class);
    }

    public function zones()
    {
        return $this->hasMany(Zone::class);
    }
}
