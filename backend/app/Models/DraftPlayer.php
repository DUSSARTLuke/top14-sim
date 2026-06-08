<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DraftPlayer extends Model
{

    protected $table = 'draft_player';
    protected $fillable = ['draft_id', 'player_id', 'position_number'];

    // Dans Draft.php
    public function players()
    {
        return $this->belongsToMany(Player::class, 'draft_player')
            ->withPivot('position_number')
            ->withTimestamps();
    }

    // Dans Player.php
    public function drafts()
    {
        return $this->belongsToMany(Draft::class, 'draft_player')
            ->withPivot('position_number')
            ->withTimestamps();
    }
}
