<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\OrganizationController;
use App\Http\Controllers\API\ReviewController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

DB::listen(function ($query) {
    Log::info('SQL', [$query->sql, $query->bindings, $query->time]);
});

// Публичные маршруты
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Защищённые маршруты (требуется аутентификация)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Организация
    Route::get('/organization', [OrganizationController::class, 'show']);
    Route::post('/organization', [OrganizationController::class, 'store']);

    // Отзывы
    Route::get('/reviews', [ReviewController::class, 'index']);
    Route::get('/reviews/summary', [ReviewController::class, 'summary']);
});
