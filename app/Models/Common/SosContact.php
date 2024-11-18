<?php

namespace App\Models\Common;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SosContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'driver_id',
        'contact_name',
        'phone_number',
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
