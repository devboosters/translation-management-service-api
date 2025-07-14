<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TranslationController;
use App\Http\Controllers\API\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    /**
     * Additional translation endpoints : I placed these end points above the resource routes because these were
     * creating conflicts with the show() action which was being called against these special routes
     */
    Route::get('translations/search', [TranslationController::class, 'search']);
    Route::get('translations/export', [TranslationController::class, 'export']);

    // Translation CRUD
    Route::apiResource('translations', TranslationController::class);

    // User management
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);
});

// Health check endpoint
Route::get('/health', function () {
    return response()->json(['status' => 'healthy']);
});
