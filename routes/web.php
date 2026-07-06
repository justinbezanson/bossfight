<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KidController;
use App\Http\Controllers\LogController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/kids', [KidController::class, 'index'])->name('kids.index');
    Route::post('/kid/create', [KidController::class, 'store'])->name('kids.store');
    Route::put('/kid/{kid}', [KidController::class, 'update'])->name('kids.update');
    Route::delete('/kid/{kid}', [KidController::class, 'destroy'])->name('kids.destroy');

    Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
});

require __DIR__.'/settings.php';
