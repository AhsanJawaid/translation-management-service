<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TranslationController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::apiResource('translations', TranslationController::class)->except(['show']);
    Route::get('/translations/search', [TranslationController::class, 'search']);
});

Route::get('/translations/export', [TranslationController::class, 'export']);