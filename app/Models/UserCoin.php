<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCoin extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_coins',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
