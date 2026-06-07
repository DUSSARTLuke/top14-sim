<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DraftPlayer extends Model
{
    protected $fillable = ['draft_id', 'player_id', 'position_number'];

    public function draft()
    {
        return $this->belongsTo(Draft::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
