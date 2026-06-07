<?php

namespace App\Services;

use App\Models\DraftPlayer;
use App\Models\Player;
use Exception;

class DraftService
{
    public function addPlayerToDraft(int $draftId, int $playerId, int $positionNumber): void
    {
        $player = Player::findOrFail($playerId);

        // Anti-doublon joueur réel
        $alreadyPicked = DraftPlayer::where('draft_id', $draftId)
            ->whereHas('player', function ($q) use ($player) {
                $q->where('real_player_id', $player->real_player_id);
            })->exists();

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
