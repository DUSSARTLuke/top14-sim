<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlayerPosition extends Model
{
    protected $fillable = ['player_id', 'position_id', 'type'];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
