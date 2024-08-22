<?php

namespace App\Services;

use App\Models\Game;

class GameTransformerService
{
    /**
     * Transform the Game model into the desired response format.
     *
     * @param Game $game
     * @return array
     */
    public function transform(Game $game): array
    {
        return [
            'board' => $game->board,
            'score' => [
                'x' => $game->x_score,
                'o' => $game->o_score,
            ],
            'currentTurn' => $game->current_turn,
            'victory' => $game->victory,
        ];
    }
}
