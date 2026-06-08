<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    public function index(Request $request)
    {
        $players = Player::with(['team', 'positions' => function ($q) {
            $q->withPivot('type');
        }])
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

    public function roll(Request $request)
    {
        $exclude = $request->input('exclude', []); // [{ team_id, season_id }]

        // Toutes les combinaisons team/season disponibles
        $query = Team::with('season')
            ->whereHas('players');

        // Exclure les équipes déjà rollées
        foreach ($exclude as $ex) {
            $query->where(function ($q) use ($ex) {
                $q->where('id', '!=', $ex['team_id'])
                    ->orWhere('season_id', '!=', $ex['season_id']);
            });
        }

        $team = $query->inRandomOrder()->first();

        if (!$team) {
            return response()->json(['message' => 'Plus d\'équipes disponibles.'], 404);
        }

        $players = Player::with(['positions' => function ($q) {
            $q->withPivot('type');
        }, 'team'])
            ->where('team_id', $team->id)
            ->get();

        return response()->json([
            'team_id'      => $team->id,
            'team_name'    => $team->name,
            'season_id'    => $team->season_id,
            'season_label' => $team->season->label,
            'players'      => $players,
        ]);
    }
}
