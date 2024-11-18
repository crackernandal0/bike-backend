<?php

namespace App\Models\Chauffeur;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChauffeurRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'chauffeur_id',
        'user_id',
        'rating',
        'review'
    ];

    // Define the relationship with Chauffeur
    public function chauffeur()
    {
        return $this->belongsTo(Chauffeur::class);
    }

    // Define the relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
