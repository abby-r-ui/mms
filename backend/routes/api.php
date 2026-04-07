<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MotorcycleController;
use App\Http\Controllers\Api\RentalController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Auth routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');

// Public motorcycles
Route::apiResource('motorcycles', MotorcycleController::class)->only(['index', 'show']);

// Auth protected
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('motorcycles', MotorcycleController::class)->except(['index', 'show']);
    Route::apiResource('rentals', RentalController::class);
});
