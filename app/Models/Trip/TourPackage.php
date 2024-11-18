<?php

namespace App\Models\Trip;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'package_name',
        'location',
        'description',
        'detailed_itinerary',
        'tour_stops_places',
        'tour_price',
        'admin_commission',
        'banner_image',
        'duration',
        'members',
        'longitude',
        'latitude',
        'is_popular',
    ];

    protected function casts(): array
    {
        return [
            'detailed_itinerary' => 'json',
            'tour_stops_places' => 'json',
        ];
    }

    public function category()
    {
        return $this->belongsTo(TripCategory::class);
    }

    public function bookings()
    {
        return $this->hasMany(UserTourBooking::class);
    }

    public function ratings()
    {
        return $this->hasMany(TourRating::class);
    }
}
