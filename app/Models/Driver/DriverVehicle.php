<?php

namespace App\Models\Driver;

use App\Models\Vehicles\VehicleModel;
use App\Models\Vehicles\VehicleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverVehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'vehicle_type_id',
        'vehicle_model',
        'registration_number',
        'registration_photo',
        'insurance_photo',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

}
