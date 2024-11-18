<?php

namespace App\Models\Driver;

use App\Models\Common\SosContact;
use App\Models\DrivingSchool\Course;
use App\Models\Ride\DriverRideRequest;
use App\Models\Ride\Ride;
use App\Models\Service\ServiceLocation;
use App\Models\Vehicles\VehicleModel;
use App\Models\Vehicles\VehicleSubcategory;
use App\Models\Vehicles\VehicleType;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Driver extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'full_name',
        'email',
        'country_code',
        'phone_number',
        'language',
        'date_of_birth',
        'address',
        'profile_photo',
        'timezone',
        'country_id',
        'active',

        'status',
        'account_status',
        'experience_years',
        'role',
        'vehicle_type_id',
        'vehicle_subcategory_id',
        'service_location_id',
        'available',
        'available_for_chauffeur',
        'available_for_trips',

        'total_accepts',
        'total_rejects',
        'total_students',
        'total_ratings',
        'instructor_bio',

        'joining_type',
        'longitude',
        'latitude',
        'fcm_token',
    ];

    // Relationships

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function vehicleModel()
    {
        return $this->belongsTo(VehicleModel::class);
    }

    public function rides()
    {
        return $this->hasMany(Ride::class);
    }

    public function vehicleSubcategory()
    {
        return $this->belongsTo(VehicleSubcategory::class);
    }

    public function serviceLocation()
    {
        return $this->belongsTo(ServiceLocation::class);
    }

    public function additionalInfo()
    {
        return $this->hasOne(DriverAdditionalInfo::class);
    }

    public function bankInfo()
    {
        return $this->hasOne(DriverBankInfo::class);
    }

    public function documents()
    {
        return $this->hasMany(DriverDocument::class);
    }

    public function vehicles()
    {
        return $this->hasMany(DriverVehicle::class);
    }
    public function sosContacts()
    {
        return $this->hasMany(SosContact::class);
    }

    public function driverRideRequests()
    {
        return $this->hasOne(DriverRideRequest::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    public function driverRequests()
    {
        return $this->hasMany(DriverRequest::class);
    }

    public function instructorRequests()
    {
        return $this->hasMany(InstructorRequest::class);
    }
}
