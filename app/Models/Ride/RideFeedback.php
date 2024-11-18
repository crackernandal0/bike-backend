<?php

namespace App\Models\Ride;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RideFeedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'ride_id',
        'rating',
        'feedback',
    ];

    public function ride()
    {
        return $this->belongsTo(Ride::class);
    }
}
