<?php

namespace App\Models\Service;

use App\Models\Vehicles\VehicleSubcategory;
use App\Models\Vehicles\VehicleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZoneTypePrice extends Model
{
    use HasFactory;

    protected $fillable = [
        'zone_id',
        'vehicle_type_id',
        'vehicle_subcategory_id',
        'payment_type',
     

        'base_price',
        'base_distance',
        'price_per_distance',
        'waiting_charge',
        'price_per_time',
        'cancellation_fee',

        'admin_commision',
        'service_tax',
        'gst_tax',

        'active',

    ];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function vehicleSubcategory()
    {
        return $this->belongsTo(VehicleSubcategory::class);
    }
}
