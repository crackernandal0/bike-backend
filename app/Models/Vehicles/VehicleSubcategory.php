<?php

namespace App\Models\Vehicles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleSubcategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'vehicle_type_id',
        'image',
        'short_amenties',
        'specifications',
        'passangers'
    ];

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'vehicle_amenities');
    }


    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    protected function casts(): array
    {
        return [
            'specifications' => 'json'
        ];
    }
}
