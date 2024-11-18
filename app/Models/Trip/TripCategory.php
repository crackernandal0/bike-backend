<?php

namespace App\Models\Trip;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function tripPackes()
    {
        return $this->hasMany(TourPackage::class);
    }
}
