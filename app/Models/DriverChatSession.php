<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverChatSession extends Model
{
    use HasFactory;

    protected $fillable = ['driver_id', 'instructor_id', 'admin_id', 'is_active', 'closed_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class); // Admin will also be from the users table
    }

    public function messages()
    {
        return $this->hasMany(DriverMessage::class);
    }
}
