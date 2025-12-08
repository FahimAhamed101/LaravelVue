<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;

// Public routes
Route::post('user/register', [UserController::class, 'store']);
Route::post('user/login', [UserController::class, 'auth']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // User routes
    Route::post('user/logout', [UserController::class, 'logout']);
    
    // Product routes - FULL CRUD
    Route::get('products', [ProductController::class, 'index']);
    Route::post('products', [ProductController::class, 'store']);
    Route::get('products/{id}', [ProductController::class, 'show']);
    Route::put('products/{id}', [ProductController::class, 'update']);
    Route::delete('products/{id}', [ProductController::class, 'destroy']);
   

Route::get('colors', function() {
    return \App\Models\Color::all();
});

Route::get('sizes', function() {
    return \App\Models\Size::all();
});
    // Alternative using apiResource (creates all CRUD routes)
    // Route::apiResource('products', ProductController::class);
});