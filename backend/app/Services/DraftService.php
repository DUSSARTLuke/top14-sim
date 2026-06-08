<?php

namespace App\Services;

use App\Models\Draft;
use App\Models\DraftPlayer;
use App\Models\Player;
use Exception;

class DraftService
{
    public function addPlayerToDraft(int $draftId, int $playerId, int $positionNumber): void
    {
        $player = Player::with('positions')->findOrFail($playerId);

        // Vérifier que le joueur peut jouer à ce poste
        $position = \App\Models\Position::where('number', $positionNumber)->first();
        if ($position) {
            $canPlay = $player->positions->contains('id', $position->id);
            if (!$canPlay) {
                throw new Exception("Ce joueur ne peut pas jouer à ce poste.");
            }
        }

        // Anti-doublon joueur réel
        $alreadyPicked = DraftPlayer::where('draft_id', $draftId)
            ->whereIn(
                'player_id',
                Player::where('real_player_id', $player->real_player_id)->pluck('id')
            )->exists();

        if ($alreadyPicked) {
            throw new Exception("Ce joueur est déjà dans votre draft.");
        }

        // Anti-doublon position
        $positionTaken = DraftPlayer::where('draft_id', $draftId)
            ->where('position_number', $positionNumber)
            ->exists();

        if ($positionTaken) {
            throw new Exception("Cette position est déjà occupée.");
        }

        DraftPlayer::create([
            'draft_id'        => $draftId,
            'player_id'       => $playerId,
            'position_number' => $positionNumber,
        ]);
    }
}
