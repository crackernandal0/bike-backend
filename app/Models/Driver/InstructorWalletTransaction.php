<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructorWalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_wallet_id',
        'transaction_id',
        'type',
        'amount',
        'reference',
        'status',
    ];

    public function wallet()
    {
        return $this->belongsTo(InstructorWallet::class);
    }
}
