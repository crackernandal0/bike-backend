<?php

namespace App\Models\Trip;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_tour_booking_id',
        'user_id',
        'transaction_id',
        'payment_type',
        'amount',
        'status',
        'provider_reference_id',
    ];

    public function UserTourBooking()
    {
        return $this->belongsTo(UserTourBooking::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
