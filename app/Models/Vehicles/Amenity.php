<?php

namespace App\Models\Vehicles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function VehicleSubcategory()
    {
        return $this->belongsToMany(VehicleSubcategory::class, 'vehicle_amenities');
    }

   
}
