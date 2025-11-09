<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompteController;
use App\Http\Controllers\Authontroller;

Route::prefix('v1')->group(function () {
    Route::post('/login', [Authontroller::class, 'login']);
    Route::post('/refresh', [Authontroller::class, 'refresh']);
    Route::middleware('auth:api')->group(function () {
        Route::get('/comptes', [CompteController::class, 'index']);
        Route::get('/comptes/{id}', [CompteController::class, 'show']);
        Route::delete('/comptes/{id}', [CompteController::class, 'destroy']);
        Route::patch('/comptes/{id}/archive', [CompteController::class, 'archive']);
        Route::patch('/comptes/{id}/unarchive', [CompteController::class, 'unarchive']);
        Route::get('/comptes-archives', [CompteController::class, 'archived']);
    });
});


