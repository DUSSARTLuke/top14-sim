<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $fillable = [
        'real_player_id',
        'name',
        'number',
        'attack',
        'defense',
        'stamina',
        'speed',
        'rating',
        'team_id',
        'season_id'
    ];

    // Toutes les versions du même joueur réel
    public function allVersions()
    {
        return $this->hasMany(Player::class, 'real_player_id', 'real_player_id');
    }
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function positions()
    {
        return $this->belongsToMany(Position::class, 'player_positions')
            ->withPivot('type')
            ->withTimestamps();
    }

    public function primaryPosition()
    {
        return $this->positions()->wherePivot('type', 'primary')->first();
    }

    public function drafts()
    {
        return $this->belongsToMany(Draft::class, 'draft_player')
            ->withPivot('position_number')
            ->withTimestamps();
    }
}
