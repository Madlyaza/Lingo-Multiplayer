<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

// Public auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Leaderboard — public so guests can view it too
Route::get('/leaderboard', [LeaderboardController::class, 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Rooms
    Route::get('/rooms',                [RoomController::class, 'index']);
    Route::post('/rooms',               [RoomController::class, 'store']);
    Route::post('/rooms/join',          [RoomController::class, 'join']);          // join by code
    Route::get('/rooms/{room}',         [RoomController::class, 'show']);
    Route::post('/rooms/{room}/join',   [RoomController::class, 'joinRoom']);      // join public room

    // Games
    Route::post('/rooms/{room}/game',   [GameController::class, 'store']);        // start game
    Route::get('/games/{game}',         [GameController::class, 'show']);
    Route::post('/games/{game}/guess',  [GameController::class, 'guess']);
});
