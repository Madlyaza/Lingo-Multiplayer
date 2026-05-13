<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Route;

// Public auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Rooms
    Route::get('/rooms',          [RoomController::class, 'index']);
    Route::post('/rooms',         [RoomController::class, 'store']);
    Route::post('/rooms/join',    [RoomController::class, 'join']);
    Route::get('/rooms/{room}',   [RoomController::class, 'show']);
});
