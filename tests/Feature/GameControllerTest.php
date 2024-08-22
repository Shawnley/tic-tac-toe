<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Game;

class GameControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_game_state()
    {
        $response = $this->getJson('/api');

        $response->assertStatus(200)
            ->assertJson([
                'board' => [
                    ["", "", ""],
                    ["", "", ""],
                    ["", "", ""],
                ],
                'score' => [
                    'x' => 0,
                    'o' => 0,
                ],
                'currentTurn' => 'x',
                'victory' => null,
            ]);
    }

    public function test_make_move()
    {
        $this->postJson('/api/x', ['x' => 0, 'y' => 0])
            ->assertStatus(200)
            ->assertJsonFragment([
                'board' => [
                    ["x", "", ""],
                    ["", "", ""],
                    ["", "", ""],
                ],
                'currentTurn' => 'o',
            ]);
    }

    public function test_move_out_of_turn()
    {
        $this->postJson('/api/x', ['x' => 0, 'y' => 0]);
        $this->postJson('/api/x', ['x' => 1, 'y' => 1])
            ->assertStatus(406);
    }

    public function test_move_on_occupied_spot()
    {
        $this->postJson('/api/x', ['x' => 0, 'y' => 0]);
        $this->postJson('/api/o', ['x' => 0, 'y' => 0])
            ->assertStatus(409);
    }

    public function test_restart_game()
    {
        $this->postJson('/api/x', ['x' => 0, 'y' => 0]);
        $this->postJson('/api/o', ['x' => 1, 'y' => 1]);
        $this->postJson('/api/x', ['x' => 0, 'y' => 1]);
        $this->postJson('/api/o', ['x' => 2, 'y' => 2]);
        $this->postJson('/api/x', ['x' => 0, 'y' => 2]);

        $response = $this->postJson('/api/restart');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'score' => [
                    'x' => 1,
                    'o' => 0,
                ],
                'board' => [
                    ["", "", ""],
                    ["", "", ""],
                    ["", "", ""],
                ],
            ]);
    }

    public function test_reset_game()
    {
        $this->postJson('/api/x', ['x' => 0, 'y' => 0]);
        $this->postJson('/api/o', ['x' => 1, 'y' => 1]);
        $this->postJson('/api/x', ['x' => 0, 'y' => 1]);
        $this->postJson('/api/o', ['x' => 2, 'y' => 2]);

        $this->postJson('/api/restart');

        $response = $this->deleteJson('/api');
        $response->assertStatus(200)
            ->assertJson(['currentTurn' => 'x']);

        $this->getJson('/api')
            ->assertJson([
                'board' => [
                    ["", "", ""],
                    ["", "", ""],
                    ["", "", ""],
                ],
                'score' => [
                    'x' => 0,
                    'o' => 0,
                ],
            ]);
    }
}
