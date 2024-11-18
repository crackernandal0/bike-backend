<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsOtp extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_code',
        'phone_number',
        'otp',
        'expires_at',
    ];
}
