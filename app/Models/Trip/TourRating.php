<?php

namespace App\Models\Trip;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'tour_package_id',
        'user_id',
        'rating',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tourPackage()
    {
        return $this->belongsTo(TourPackage::class);
    }
}
