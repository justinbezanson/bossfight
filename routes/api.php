<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/log/create', [LogController::class, 'store']);

    Route::get('/games', [GameController::class, 'index']);
});
