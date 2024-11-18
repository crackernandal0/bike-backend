<?php

namespace App\Models\DrivingSchool;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'location',
        'status',
        'course_status',
        'completed_at',
        'enrollment_id',
    ];

    protected static function boot()
    {
        parent::boot();

        // Automatically generate the enrollment ID when creating a new enrollment
        static::creating(function ($enrollment) {
            $enrollment->enrollment_id = 'FEMI-' . rand(100000, 999999);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
