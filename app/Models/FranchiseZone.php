<?php

namespace App\Models;

use App\Models\Service\Zone;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FranchiseZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'zone_id',
        'franchise_id',
    ];



    public function franchise()
    {
        return $this->belongsToMany(Franchise::class);
    }

    public function zones()
    {
        return $this->belongsTo(Zone::class);
    }
}
