<?php

namespace App\Models\Service;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promo extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_location_id',
        'code',
        'minimum_trip_amount',
        'maximum_discount_amount',
        'discount_percentage',
        'max_usage',
        'usage_count',
        'from',
        'to',
        'active',
    ];

    public function serviceLocation()
    {
        return $this->belongsTo(ServiceLocation::class);
    }
}
