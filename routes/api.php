<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\Api\DishController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\TableController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

// Lectura pública limitada
Route::get('restaurants', [RestaurantController::class, 'index']);
Route::get('restaurants/{restaurant}', [RestaurantController::class, 'show']);
Route::get('dishes', [DishController::class, 'index']);
Route::get('dishes/most-ordered', [DishController::class, 'mostOrdered']);
Route::get('dishes/{dish}', [DishController::class, 'show']);
Route::get('tables/available', [TableController::class, 'available']);

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('restaurants', RestaurantController::class)->except(['index', 'show']);
    Route::apiResource('tables', TableController::class);
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('employees', EmployeeController::class);
    Route::apiResource('dishes', DishController::class)->except(['index', 'show']);

    Route::get('reservations/{reservation}/total', [ReservationController::class, 'total']);
    Route::apiResource('reservations', ReservationController::class);

    Route::apiResource('orders', OrderController::class)
        ->middleware('permission:orders.manage');
});
