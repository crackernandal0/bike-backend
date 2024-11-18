<?php

namespace App\Models\Driver;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverNotification extends Model
{
    use HasFactory;

    protected $fillable = ['driver_id', 'instructor_id', 'title', 'body'];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
    public function instructor()
    {
        return $this->belongsTo(Driver::class, 'instructor_id');
    }
}
