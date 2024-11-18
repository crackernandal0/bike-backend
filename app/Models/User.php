<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\Common\FavouriteLocation;
use App\Models\Common\SosContact;
use App\Models\Ride\Ride;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'country_code',
        'phone_number',
        'profile_picture',
        'address',
        'country_id',
        'timezone',
        'language',
        'active',
        'email_confirmed',
        'mobile_confirmed',
        'fcm_token',
        'referral_code',
        'referred_by',
        'social_provider',
        'social_id',
        'login_device',
        'last_known_ip',
        'last_active_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function rides()
    {
        return $this->hasMany(Ride::class);
    }
    public function sosContacts()
    {
        return $this->hasMany(SosContact::class);
    }
    public function favoriteLocations()
    {
        return $this->hasMany(FavouriteLocation::class);
    }
    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'user_id');
    }
}
