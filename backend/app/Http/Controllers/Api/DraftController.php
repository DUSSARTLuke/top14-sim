<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Draft;
use App\Services\DraftService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DraftController extends Controller
{
    public function __construct(private DraftService $draftService) {}

    private function canAccessDraft(Draft $draft, Request $request): bool
    {
        if ($request->user()) {
            return $draft->user_id === $request->user()->id;
        }

        return $draft->session_token === $request->header('X-Session-Token');
    }

    public function index(Request $request)
    {
        if ($request->user()) {
            $drafts = Draft::where('user_id', $request->user()->id)
                ->with(['season', 'players'])
                ->get();
        } else {
            $drafts = Draft::where('session_token', $request->header('X-Session-Token'))
                ->with(['season', 'players'])
                ->get();
        }

        return response()->json($drafts);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'season_id' => 'required|exists:seasons,id',
        ]);

        $draft = Draft::create([
            'name'          => $data['name'],
            'season_id'     => $data['season_id'],
            'user_id'       => $request->user()?->id,
            'session_token' => $request->user() ? null : Str::uuid(),
        ]);

        return response()->json($draft->load('season'), 201);
    }

    public function show(Request $request, Draft $draft)
    {
        abort_if(!$this->canAccessDraft($draft, $request), 403, 'Accès refusé.');

        return response()->json($draft->load(['season', 'players.team', 'players.positions']));
    }

    public function update(Request $request, Draft $draft)
    {
        abort_if(!$this->canAccessDraft($draft, $request), 403, 'Accès refusé.');

        $draft->update($request->validate(['name' => 'required|string|max:255']));

        return response()->json($draft);
    }

    public function destroy(Request $request, Draft $draft)
    {
        abort_if(!$this->canAccessDraft($draft, $request), 403, 'Accès refusé.');

        $draft->delete();

        return response()->json(null, 204);
    }

    public function addPlayer(Request $request, Draft $draft)
    {
        abort_if(!$this->canAccessDraft($draft, $request), 403, 'Accès refusé.');

        $data = $request->validate([
            'player_id'       => 'required|exists:players,id',
            'position_number' => 'required|integer|min:1|max:15',
        ]);

        try {
            $this->draftService->addPlayerToDraft($draft->id, $data['player_id'], $data['position_number']);
            return response()->json($draft->load('players'), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
        }
    }

    public function removePlayer(Request $request, Draft $draft, $playerId)
    {
        abort_if(!$this->canAccessDraft($draft, $request), 403, 'Accès refusé.');

        $draft->players()->detach($playerId);

        return response()->json($draft->load('players'));
    }
}
