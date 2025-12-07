<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('user/register',[UserController::class,'store']);

Route::post('user/login',[UserController::class,'auth']);

Route::post('user/logout',[UserController::class,'logout'])->middleware('auth:sanctum');