<?php

namespace App\Models\Vehicles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand_id',
        'vehicle_subcategory_id',

        'type_id',
        'model_id',
        'color',
        'license_plate',
        'year',
        'image',
        'vehicle_passing_till',
        'special_services',
        'status',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function vehicle_subcategory()
    {
        return $this->belongsTo(VehicleSubcategory::class, 'vehicle_subcategory_id');
    }

   
    public function type()
    {
        return $this->belongsTo(VehicleType::class, 'type_id');
    }

    public function model()
    {
        return $this->belongsTo(VehicleModel::class, 'model_id');
    }


  
}
