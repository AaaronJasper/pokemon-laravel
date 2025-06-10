<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'sender_pokemon_id',
        'receiver_pokemon_id',
        'status'
    ];

    public function sender() {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function receiver() {
        return $this->belongsTo(User::class, 'receiver_id', 'id');
    }

    public function senderPokemon() {
        return $this->belongsTo(Pokemon::class, 'sender_pokemon_id', 'id');
    }

    public function receiverPokemon() {
        return $this->belongsTo(Pokemon::class, 'receiver_pokemon_id', 'id');
    }
}
