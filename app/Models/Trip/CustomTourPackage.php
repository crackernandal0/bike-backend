<?php

namespace App\Models\Trip;

use App\Models\User;
use App\Models\Vehicles\VehicleSubcategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomTourPackage extends Model
{
    use HasFactory;

  
    protected $fillable = [
        'user_id',
        'pickup_location',
        'vehicle_subcategory_id',
        'tour_location',
        'start_date',
        'return_date',
        'no_of_passengers',
        'special_requests',
        'budget',
        'special_requests',
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

   
    public function vehicleSubcategory()
    {
        return $this->belongsTo(VehicleSubcategory::class);
    }
}
