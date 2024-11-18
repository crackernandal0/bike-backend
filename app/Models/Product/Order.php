<?php

namespace App\Models\Product;

use App\Models\Driver\Driver;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'driver_id',
        'address',
        'phone',
        'payment_method',
        'payment_status',
        'total_price',
        'order_status',
        'delivery_status',
    ];

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function requests()
    {
        return $this->hasMany(DriverOrderRequest::class);
    }
}
