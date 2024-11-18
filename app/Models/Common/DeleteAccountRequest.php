<?php

namespace App\Models\Common;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeleteAccountRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'driver_id',
        'name',
        'email',
        'phone_number',
        'profile_picture',
        'referral_code',
        'reason',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
