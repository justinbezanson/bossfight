<?php

use App\Http\Controllers\LogController;
use App\Http\Middleware\EnsureApiKey;
use Illuminate\Support\Facades\Route;

Route::middleware(EnsureApiKey::class)->group(function () {
    Route::post('/log/create', [LogController::class, 'store']);
});
