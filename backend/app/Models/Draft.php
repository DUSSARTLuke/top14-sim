<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Draft extends Model
{
    protected $fillable = ['user_id', 'season_id', 'name', 'session_token'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function players()
    {
        return $this->belongsToMany(Player::class, 'draft_player')
            ->withPivot('position_number')
            ->withTimestamps();
    }
}
