<?php

namespace App\Models\Trip;

use App\Models\User;
use App\Models\Vehicles\VehicleSubcategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTourBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_package_id',
        'user_id',
        'pickup_location',
        'vehicle_subcategory_id',
        'booking_date',
        'no_of_passengers',
        'special_requests',
        'promo_code',
        'payment_status',
        'payment_method',
        'booking_status',
        'stops',
    ];

    protected function casts(): array
    {
        return [
            'stops' => 'json',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tourPackage()
    {
        return $this->belongsTo(TourPackage::class);
    }

    public function vehicleSubcategory()
    {
        return $this->belongsTo(VehicleSubcategory::class);
    }
}
