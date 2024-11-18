<?php

namespace App\Models\DrivingSchool;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonProgress extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'lesson_id', 'status', 'started_at', 'completed_at', 'learning_day', 'learning_time'];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with Lesson
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
