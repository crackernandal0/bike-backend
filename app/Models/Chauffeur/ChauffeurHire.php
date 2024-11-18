<?php

namespace App\Models\Chauffeur;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChauffeurHire extends Model
{
    protected $table = "chauffeur_hire";
    use HasFactory;

    protected $fillable = [
        'user_id',
        'chauffeur_id',
        'pickup',
        'dropoff',
        'pickup_location_type',
        'destination_location_type',
        'date',
        'start_time',
        'end_time',
        'vehicle_type',
        'preferred_vehicle',
        'chauffeur_type',
        'hire_type',
        'event_type',
        'child_seats',
        'specific_vehicle_models',
        'additional_amenities',
        'additional_requests',
        'price',
        'admin_commission',
        'gst',
        'service_tax',
        'status',
        'payment_status',
    ];

    // Define relationships if any, e.g., with User or Chauffeur
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function chauffeur()
    {
        return $this->belongsTo(Chauffeur::class);
    }
}
