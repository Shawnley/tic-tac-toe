<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

Route::get('/', [GameController::class, 'getGameState']);
Route::post('/restart', [GameController::class, 'restartGame']);
Route::post('/{piece}', [GameController::class, 'makeMove']);
Route::delete('/', [GameController::class, 'resetGame']);
