<?php

namespace App\Models;

use App\Models\Service\Zone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Franchise extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'owner_name',
        'contact_number',
        'email',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'status',
        'start_date',
        'end_date',
        'franchise_fee',
        'royalty_percentage',
        'website_url',
    ];

    public function franchiseZones()
    {
        return $this->belongsTo(FranchiseZone::class);
    }

   
}
