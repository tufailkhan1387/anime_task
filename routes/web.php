<?php

use App\Http\Controllers\AnimeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['api', 'throttle:60,1'])->get('/api/anime/{slug}', [AnimeController::class, 'show']);
