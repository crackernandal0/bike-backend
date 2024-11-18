<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_location_id',
        'name',
        'coordinates',
        'active',
    ];

    public function serviceLocation()
    {
        return $this->belongsTo(ServiceLocation::class);
    }

    public function zoneTypePrice()
    {
        return $this->hasMany(ZoneTypePrice::class);
    }
}
