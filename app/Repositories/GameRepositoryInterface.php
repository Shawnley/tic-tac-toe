<?php

namespace App\Repositories;

use App\Models\Game;

interface GameRepositoryInterface
{
    public function getGame(): Game;
    public function createGame(): Game;
    public function updateGame(Game $game, array $data): Game;
    public function deleteGame(Game $game): void;
}
