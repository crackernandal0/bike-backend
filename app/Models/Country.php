<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'dial_code',
        'dial_min_length',
        'dial_max_length',
        'code',
        'currency_name',
        'currency_code',
        'currency_symbol',
        'flag',
        'active',
    ];
}
