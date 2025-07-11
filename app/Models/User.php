<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
   //use HasProfilePhoto;
    use Notifiable;
    //use TwoFactorAuthenticatable;

    public function pokemon()
    {
        return $this->hasMany(Pokemon::class, 'user_id', 'id');
    }

    public function sentTrades()
    {
        return $this->hasMany(Trade::class, 'sender_id', 'id');
    }

    public function receivedTrades()
    {
        return $this->hasMany(Trade::class, 'receiver_id', 'id');
    }

    public function likedPokemons()
    {
        return $this->belongsToMany(Pokemon::class, 'pokemon_user_likes')->withTimestamps();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_account',
        'email_verified_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
