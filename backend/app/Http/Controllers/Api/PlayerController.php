<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index(Request $request)
    {
        $players = Player::with(['team', 'positions'])
            ->when($request->season_id, fn($q) => $q->where('season_id', $request->season_id))
            ->when($request->team_id,   fn($q) => $q->where('team_id', $request->team_id))
            ->when($request->position_id, fn($q) => $q->whereHas(
                'positions',
                fn($q2) =>
                $q2->where('position_id', $request->position_id)
            ))
            ->paginate(20);

        return response()->json($players);
    }

    public function show(Player $player)
    {
        return response()->json($player->load(['team', 'season', 'positions']));
    }
}
