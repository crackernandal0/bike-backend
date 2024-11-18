<?php

namespace App\Models\Common;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactQuery extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'driver_id',
        'subject',
        'message',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
