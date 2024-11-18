<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverBankInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'bank_account_number',
        'ifsc_code',
        'account_holder_name',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
