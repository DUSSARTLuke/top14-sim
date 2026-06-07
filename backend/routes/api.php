<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\SeasonController;
use App\Http\Controllers\Api\DraftController;
use App\Http\Controllers\Api\PositionController;

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Public — pas besoin de connexion
Route::apiResource('seasons',   SeasonController::class)->only(['index', 'show']);
Route::apiResource('teams',     TeamController::class)->only(['index', 'show']);
Route::apiResource('positions', PositionController::class)->only(['index', 'show']);
Route::apiResource('players',   PlayerController::class)->only(['index', 'show']);

// Drafts publics
Route::apiResource('drafts', DraftController::class);
Route::post('/drafts/{draft}/players',            [DraftController::class, 'addPlayer']);
Route::delete('/drafts/{draft}/players/{player}', [DraftController::class, 'removePlayer']);

// Protégé — suivi des simulations
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(Request $request) => $request->user());
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user/simulations', [SimulationController::class, 'history']);
});
