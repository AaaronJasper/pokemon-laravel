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
        'skill4_id'
    ];

    public function nature()
    {
        return $this->belongsTo(Nature::class, 'nature_id', 'id');
    }
    public function ability()
    {
        return $this->belongsTo(Ability::class, 'ability_id', 'id');
    }
}
