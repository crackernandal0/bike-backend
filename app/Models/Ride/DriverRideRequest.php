<?php

namespace App\Models\Ride;

use App\Models\Driver\Driver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverRideRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'ride_id',
        'driver_id',
        'request_status',
        'accepted_at',
        'declined_at',
    ];


    public function ride()
    {
        return $this->belongsTo(Ride::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
