<?php

namespace App\Models\Chauffeur;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChauffeurHirePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'chauffeur_hire_id',
        'user_id',
        'transaction_id',
        'payment_type',
        'amount',
        'status',
        'provider_reference_id',
    ];

    public function chauffeurHire()
    {
        return $this->belongsTo(ChauffeurHire::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
