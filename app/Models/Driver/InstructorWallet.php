<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructorWallet extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'balance',
        'is_active',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function transactions()
    {
        return $this->hasMany(InstructorWalletTransaction::class);
    }
}
