<?php

namespace App\Models\DrivingSchool;

use App\Models\Driver\Driver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'title',
        'subtitle',
        'price',
        'admin_commision',
        'description',
        'features',
        'curriculum_title',
        'curriculum',
        'total_enrollments',
        'banner_image',
        'duration',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'features' => 'json',
            'curriculum' => 'json'
        ];
    }

    // Define the relationship with the Instructor
    public function instructor()
    {
        return $this->belongsTo(Driver::class,'instructor_id','id');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
    public function ratings()
    {
        return $this->hasMany(CourseRating::class);
    }
}
