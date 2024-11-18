<?php

namespace App\Models\Vehicles;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;


    protected $fillable = [
        'name', 
        'icon',
        'status',
    ];

    public function models()
    {
        return $this->hasMany(VehicleModel::class);
    }

}
