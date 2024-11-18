<?php

namespace App\Models\Ride;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RidePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'driver_id',
        'ride_id',
        'merchant_transaction_id',
        'payment_amount',
        'provider_reference_id',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function ride()
    {
        return $this->belongsTo(Ride::class);
    }

    
}
