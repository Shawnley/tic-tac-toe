<?php

namespace App\Http\Controllers;

use App\Models\Game;
use App\Services\GameService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\MakeMoveRequest;
use App\Http\Resources\GameTransformer;
use App\Services\GameTransformerService;
use Illuminate\Http\Request;

class GameController extends Controller
{
    protected GameService $gameService;
    protected GameTransformerService $gameTransformerService;


    public function __construct(GameService $gameService, GameTransformerService $gameTransformerService)
    {
        $this->gameService = $gameService;
        $this->gameTransformerService = $gameTransformerService;
    }

    /**
     * Get the current game state.
     * 
     * @return JsonResponse
     */
    public function getGameState()
    {
        $game = $this->gameService->getGame();

        // Use the GameTransformerService to format the response
        $formattedResponse = $this->gameTransformerService->transform($game);

        return response()->json($formattedResponse);
    }

    /**
     * Make a move in the game.
     * 
     * @param MakeMoveRequest $request
     * @param string $piece
     * @return JsonResponse
     */
    public function makeMove(MakeMoveRequest $request, string $piece)
    {
        try {
            $game = $this->gameService->getGame();
            $updatedGame = $this->gameService->makeMove($game, $piece, $request->x, $request->y);

            // If the game is still ongoing, make an AI move
            if (!$updatedGame->victory && !$this->gameService->isBoardFull($updatedGame->board) && $request->opponent === 'ai') {
                $updatedGame = $this->gameService->makeAIMove($game, $updatedGame->current_turn);
            }

            // Use the GameTransformerService to format the response
            $formattedResponse = $this->gameTransformerService->transform($updatedGame);

            return response()->json($formattedResponse);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }


    /**
     * Restart the game and update scores.
     * 
     * @return JsonResponse
     */
    public function restartGame()
    {
        $game = $this->gameService->getGame();
        $updatedGame = $this->gameService->restartGame($game);

        // Use the GameTransformerService to format the response
        $formattedResponse = $this->gameTransformerService->transform($updatedGame);

        return response()->json($formattedResponse);
    }

    /**
     * Reset the game and scores.
     * 
     * @return JsonResponse
     */
    public function resetGame()
    {
        $game = $this->gameService->getGame();
        $newGame = $this->gameService->resetGame($game);

        // Use the GameTransformerService to format the response
        $formattedResponse = $this->gameTransformerService->transform($newGame);

        return response()->json($formattedResponse);
    }
}
