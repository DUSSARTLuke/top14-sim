<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = [
        'name',
        'code',
        'number',
        'zone',
        'weight_attack',
        'weight_defense',
        'weight_stamina',
        'weight_speed'
    ];

    public function players()
    {
        return $this->belongsToMany(Player::class, 'player_positions')
            ->withPivot('type')
            ->withTimestamps();
    }
}
