<?php

namespace App\Repositories;

use App\Models\Game;

class GameRepository implements GameRepositoryInterface
{
    /**
     * Retrieve the existing game or create a new one.
     * 
     * @return Game
     */
    public function getGame(): Game
    {
        return Game::first() ?? $this->createGame();
    }

    /**
     * Create a new game.
     * 
     * @return Game
     */
    public function createGame(): Game
    {
        return Game::create([
            'board' => [["", "", ""], ["", "", ""], ["", "", ""]],
            'x_score' => 0,
            'o_score' => 0,
            'current_turn' => 'x',
            'victory' => null,
        ]);
    }

    /**
     * Update an existing game.
     */
    public function updateGame(Game $game, array $data): Game
    {
        $game->update($data);
        return $game;
    }

    /**
     * Delete a game.
     */
    public function deleteGame(Game $game): void
    {
        $game->delete();
    }
}
