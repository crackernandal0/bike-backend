<?php

namespace App\Models\Ride;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RideStop extends Model
{
    use HasFactory;


    protected $fillable = [
        'ride_id',
        'stop',
    ];

    public function ride()
    {
        return $this->belongsTo(Ride::class);
    }

    protected function casts(): array
    {
        return [
            'stop' => 'json',
        ];
    }
}
