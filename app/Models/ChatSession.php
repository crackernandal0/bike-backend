<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatSession extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'admin_id', 'is_active', 'closed_at'];

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
        return $this->hasMany(Message::class);
    }
}
