<?php

namespace Tests\Unit;

use App\Models\Game;
use App\Services\GameService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameServiceTest extends TestCase
{
    use RefreshDatabase;

    protected GameService $gameService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->gameService = $this->app->make(GameService::class);
    }

    public function test_game_initializes_correctly()
    {
        $game = $this->gameService->getGame();

        $this->assertEquals([["", "", ""], ["", "", ""], ["", "", ""]], $game->board);
        $this->assertEquals('x', $game->current_turn);
        $this->assertEquals(0, $game->x_score);
        $this->assertEquals(0, $game->o_score);
        $this->assertNull($game->victory);
    }

    public function test_valid_move_updates_board()
    {
        $game = $this->gameService->getGame();
        $game = $this->gameService->makeMove($game, 'x', 0, 0);

        $this->assertEquals('x', $game->board[0][0]);
        $this->assertEquals('o', $game->current_turn);
    }

    public function test_invalid_move_out_of_turn()
    {
        $game = $this->gameService->getGame();

        $this->expectException(\Exception::class);
        $this->gameService->makeMove($game, 'o', 0, 0); // Should fail because it's X's turn
    }

    public function test_invalid_move_on_occupied_space()
    {
        $game = $this->gameService->getGame();
        $game = $this->gameService->makeMove($game, 'x', 0, 0);

        $this->expectException(\Exception::class);
        $this->gameService->makeMove($game, 'o', 0, 0); // Should fail because the spot is occupied
    }

    public function test_victory_is_correctly_identified()
    {
        $game = $this->gameService->getGame();

        // Simulate a winning move for 'x'
        $game->board = [
            ['x', 'x', ''],
            ['o', 'o', ''],
            ['', '', '']
        ];
        $game = $this->gameService->makeMove($game, 'x', 0, 2); // x wins

        $this->assertEquals('x', $game->victory);
    }

    public function test_no_victory_for_incomplete_game()
    {
        $game = $this->gameService->getGame();

        // Simulate a non-winning move
        $game->board = [
            ['x', '', ''],
            ['o', '', ''],
            ['', '', '']
        ];
        $game = $this->gameService->makeMove($game, 'x', 0, 1); // No one wins

        $this->assertNull($game->victory);
    }

    public function test_game_restarts_correctly()
    {
        $game = $this->gameService->getGame();

        // Simulate a winning scenario
        $game->board = [
            ['x', 'x', 'x'],
            ['o', 'o', ''],
            ['', '', '']
        ];
        $game->victory = 'x';
        $game->save();

        $this->gameService->updateScore($game, 'x');

        $game = $this->gameService->restartGame($game);

        $this->assertEquals([["", "", ""], ["", "", ""], ["", "", ""]], $game->board);
        $this->assertEquals(1, $game->x_score);
        $this->assertEquals(0, $game->o_score);
        $this->assertNull($game->victory);
        $this->assertEquals('x', $game->current_turn);
    }

    public function test_ai_makes_valid_move()
    {
        // Assuming you have a method for AI move in the GameService
        $game = $this->gameService->getGame();

        // Simulate the AI's turn
        $game->current_turn = 'o';
        $game = $this->gameService->makeAiMove($game, 'o');

        // Check that 'o' made a valid move
        $this->assertTrue(in_array('o', array_merge(...$game->board)));
    }
}
