<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pokemon extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'level',
        'race',
        'nature_id',
        'ability_id',
        'skill1_id',
        'skill2_id',
        'skill3_id',
        'skill4_id',
        'user_id',
        'image_url',
        'is_trading',
    ];

    public function nature()
    {
        return $this->belongsTo(Nature::class, 'nature_id', 'id');
    }
    public function ability()
    {
        return $this->belongsTo(Ability::class, 'ability_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function senderPokemon() 
    {
        return $this->hasMany(Trade::class, 'sender_pokemon_id', 'id');
    }
    public function receiverPokemon() 
    {
        return $this->hasMany(Trade::class, 'receiver_pokemon_id', 'id');
    } 
    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'pokemon_user_likes')->withTimestamps();
    }

}
