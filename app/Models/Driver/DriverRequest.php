<?php

namespace App\Models\Driver;

use App\Models\Driver\Driver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'available_for_chauffeur',
        'available_for_trips',
        'joining_type',
        'additional_requests',
        'status'
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
