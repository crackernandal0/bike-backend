<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ['chat_session_id', 'user_id', 'admin_id', 'sender_type', 'message', 'image'];

    public function chatSession()
    {
        return $this->belongsTo(ChatSession::class);
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
