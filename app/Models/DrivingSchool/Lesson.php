<?php

namespace App\Models\DrivingSchool;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'duration',
        'resources',
    ];

    protected function casts(): array
    {
        return [
            'resources' => 'json',
        ];
    }

    // Define the relationship with the Course
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
    public function lesson_progress()
    {
        return $this->hasOne(LessonProgress::class);
    }
}
