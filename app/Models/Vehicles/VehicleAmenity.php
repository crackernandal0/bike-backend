<?php

namespace App\Models\Vehicles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleAmenity extends Model
{
    use HasFactory;

    protected $table = 'vehicle_amenities';

    
    protected $fillable = [
        'vehicle_model_id',
        'amenity_id',
    ];
}
