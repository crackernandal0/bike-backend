<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverAdditionalInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        
        'additional_requests',
        'service_preferences',
        'available_from',
        'availability_schedule',
        'emergency_contact_name',
        'emergency_contact_number',

        'qualifications',
        'qualifications_attachments',
        'certifications',
        'training_specializations',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    protected function casts(): array
    {
        return [
            'qualifications_attachments' => 'json',
            'certifications' => 'json'
        ];
    }

}
