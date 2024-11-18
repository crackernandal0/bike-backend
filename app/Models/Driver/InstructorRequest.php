<?php

namespace App\Models\Driver;

use App\Models\Driver\Driver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructorRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'qualifications',
        'qualifications_attachments',
        'certifications',
        'training_specializations',
        'additional_requests',
        'status'
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
