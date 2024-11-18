<?php

namespace App\Models\Chauffeur;

use App\Models\Driver\Driver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chauffeur extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'tagline',
        'description',
        'image',
        'skills_certifications',
        'additional_services',
        'availability',
        'status'
    ];

    protected function casts(): array
    {
        return [
            'skills_certifications' => 'json',
            'additional_services' => 'json'
        ];
    }
    // Define the relationship with Driver
    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    // Define the relationship with ChauffeurRating
    public function ratings()
    {
        return $this->hasMany(ChauffeurRating::class);
    }
}
