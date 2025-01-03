<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverMessage extends Model
{
    use HasFactory;


    protected $fillable = ['driver_chat_session_id', 'driver_id', 'instructor_id', 'admin_id', 'sender_type', 'message', 'image'];

    public function chatSession()
    {
        return $this->belongsTo(DriverChatSession::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
