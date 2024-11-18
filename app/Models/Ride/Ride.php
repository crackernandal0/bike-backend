<?php

namespace App\Models\Ride;

use App\Models\Driver\Driver;
use App\Models\Service\Promo;
use App\Models\Service\ServiceLocation;
use App\Models\Service\Zone;
use App\Models\Service\ZoneTypePrice;
use App\Models\User;
use App\Models\Vehicles\VehicleSubcategory;
use App\Models\Vehicles\VehicleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ride extends Model
{
    use HasFactory;

    protected $fillable = [
        'ride_number',
        'ride_status',
        'ride_otp',
        'instant_ride',
        'ride_later',
        'user_id',
        'service_location_id',
        'vehicle_type_id',
        'vehicle_subcategory_id',
        'zone_id',
        'zone_type_id',
        'driver_id',
        'is_schedule_ride',
        'scheduled_date',
        'scheduled_time',
        'ride_type',
        'return_trip',
        'return_date',
        'return_time',
        'passenger_count',
        "is_for_someone_else",
        "rider_name",
        "rider_phone_number",
        "additional_notes",
        'ride_accepted_at',
        'driver_arrived_at',
        'ride_started_at',
        'ride_completed_at',
        'ride_cancelled_at',
        'cancel_type',
        'cancel_reason',
        'canceled_by',
        'cancellation_fee',
        'payment_type',
        'payment_amount',
        'final_fare',
        'user_coins_discount',
        'payment_status',
        'ride_booked_at',
        'total_distance',
        'estimated_time',
        'waiting_minutes',
        'waiting_charges',
        'promo_id',
        'pickup',
        'dropoff',
    ];

    protected function casts(): array
    {
        return [
            'pickup' => 'json',
            'dropoff' => 'json',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceLocation()
    {
        return $this->belongsTo(ServiceLocation::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function zoneType()
    {
        return $this->belongsTo(ZoneTypePrice::class, 'zone_type_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class)
            ->where('status', 'approved');
    }

    public function promo()
    {
        return $this->belongsTo(Promo::class);
    }

    public function rideStops()
    {
        return $this->hasMany(RideStop::class);
    }

    public function feedback()
    {
        return $this->hasOne(RideFeedback::class);
    }

    public function ridePayment()
    {
        return $this->hasOne(RidePayment::class);
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
