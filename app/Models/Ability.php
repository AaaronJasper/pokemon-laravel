<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ability extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
    ];

    public function pokemon()
    {
        return $this->hasMany(Pokemon::class, 'ability_id', 'id');
    }
}
