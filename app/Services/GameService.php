<?php

namespace App\Services;

use App\Models\Game;
use App\Repositories\GameRepositoryInterface;

class GameService
{
    protected $gameRepository;

    public function __construct(GameRepositoryInterface $gameRepository)
    {
        $this->gameRepository = $gameRepository;
    }

    /**
     * Initialize the game if it doesn't exist or return the existing game.
     * 
     * @return Game
     */
    public function getGame()
    {
        return $this->gameRepository->getGame();
    }

    /**
     * Make a move in the game, updating the board and checking for a winner.
     * 
     * @param Game $game
     * @param string $piece
     * @param int $x
     * @param int $y
     * @return Game
     */
    public function makeMove(Game $game, string $piece, int $x, int $y)
    {
        // Ensure the position is empty
        if ($game->board[$x][$y] !== "") {
            throw new \InvalidArgumentException('Position already occupied', 409);
        }

        // Ensure it's the correct player's turn
        if ($game->current_turn !== $piece) {
            throw new \LogicException('Not your turn', 406);
        }

        // Place the piece on the board
        $board = $game->board;
        $board[$x][$y] = $piece;
        $game->board = $board;

        // Check for victory
        $victory = $this->checkVictory($board);
        if ($victory) {
            $game->victory = $victory;
            $this->updateScore($game, $victory);
        } else {
            $game->current_turn = $piece === 'x' ? 'o' : 'x';
        }

        $game->save();

        return $game;
    }

    /**
     * Check if the board has a winning combination.
     * 
     * @param array $board
     * @return string|null
     */
    public function checkVictory(array $board)
    {
        $winningCombinations = collect([
            [[0, 0], [0, 1], [0, 2]],
            [[1, 0], [1, 1], [1, 2]],
            [[2, 0], [2, 1], [2, 2]],
            [[0, 0], [1, 0], [2, 0]],
            [[0, 1], [1, 1], [2, 1]],
            [[0, 2], [1, 2], [2, 2]],
            [[0, 0], [1, 1], [2, 2]],
            [[0, 2], [1, 1], [2, 0]],
        ]);

        foreach ($winningCombinations as $combination) {
            [$a, $b, $c] = $combination;

            if (
                $board[$a[0]][$a[1]] !== "" &&
                $board[$a[0]][$a[1]] === $board[$b[0]][$b[1]] &&
                $board[$a[0]][$a[1]] === $board[$c[0]][$c[1]]
            ) {
                return $board[$a[0]][$a[1]]; // Return the winning piece ('x' or 'o')
            }
        }

        return null; // No winner, return null
    }

    /**
     * Update the game score based on the winner.
     * 
     * @param Game $game
     * @param string $winner
     * @return void
     */
    public function updateScore(Game $game, string $winner)
    {
        $scoreField = $winner . '_score';
        $game->increment($scoreField);
    }

    /**
     * Reset the game board and state for a new game.
     * 
     * @param Game $game
     * @return Game
     */
    public function restartGame(Game $game)
    {
        // Use repository to update the game
        return $this->gameRepository->updateGame($game, [
            'board' => [["", "", ""], ["", "", ""], ["", "", ""]],
            'current_turn' => 'x',
            'victory' => null,
        ]);
    }

    /**
     * Reset the entire game including scores.
     * 
     * @param Game $game
     * @return Game
     */
    public function resetGame(Game $game)
    {
        $this->gameRepository->deleteGame($game);
        return $this->gameRepository->createGame();
    }

    /**
     * Make a move for the AI player.
     * 
     * @param Game $game
     * @param string $piece
     * @return Game
     */
    public function makeAIMove(Game $game, string $piece)
    {
        $bestMove = $this->findBestMove($game->board, $piece);
        return $this->makeMove($game, $piece, $bestMove['x'], $bestMove['y']);
    }

    /**
     * Find the best move for the AI player using the minimax algorithm.
     * 
     * @param array $board
     * @param string $piece
     * @return array
     */
    private function findBestMove(array $board, string $piece)
    {
        $bestScore = -INF;
        $bestMove = [];

        collect(range(0, 2))->crossJoin(range(0, 2))->each(function ($position) use (&$bestScore, &$bestMove, $board, $piece) {
            [$x, $y] = $position;

            if ($board[$x][$y] === "") {
                $board[$x][$y] = $piece;
                $score = $this->minimax($board, 0, false, $piece);
                $board[$x][$y] = "";

                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestMove = ['x' => $x, 'y' => $y];
                }
            }
        });

        return $bestMove;
    }

    /**
     * Minimax algorithm to determine the best move for the AI player.
     * 
     * @param array $board
     * @param int $depth
     * @param bool $isMaximizing
     * @param string $piece
     * @return int
     */
    private function minimax(array $board, int $depth, bool $isMaximizing, string $piece)
    {
        $opponentPiece = $piece === 'x' ? 'o' : 'x';
        $result = $this->checkVictory($board);

        if ($result === $piece) {
            return 10 - $depth;
        } elseif ($result === $opponentPiece) {
            return $depth - 10;
        } elseif ($this->isBoardFull($board)) {
            return 0;
        }

        if ($isMaximizing) {
            $bestScore = -INF;
            foreach ($board as $i => $row) {
                foreach ($row as $j => $cell) {
                    if ($cell === "") {
                        $board[$i][$j] = $piece;
                        $score = $this->minimax($board, $depth + 1, false, $piece);
                        $board[$i][$j] = "";
                        $bestScore = max($score, $bestScore);
                    }
                }
            }
            return $bestScore;
        } else {
            $bestScore = INF;
            foreach ($board as $i => $row) {
                foreach ($row as $j => $cell) {
                    if ($cell === "") {
                        $board[$i][$j] = $opponentPiece;
                        $score = $this->minimax($board, $depth + 1, true, $piece);
                        $board[$i][$j] = "";
                        $bestScore = min($score, $bestScore);
                    }
                }
            }
            return $bestScore;
        }
    }

    /**
     * Check if the board is full.
     * 
     * @param array $board
     * @return bool
     */
    public function isBoardFull(array $board)
    {
        return collect($board)->flatten()->every(fn($cell) => $cell !== "");
    }
}
