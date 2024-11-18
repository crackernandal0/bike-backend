<?php

namespace App\Models\Vehicles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon'
    ];

    public function vehicles()
    {
        return $this->hasMany(VehicleModel::class);
    }
    public function subCategories()
    {
        return $this->hasMany(VehicleSubcategory::class);
    }
}
