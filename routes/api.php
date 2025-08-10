<?php

use App\Http\Controllers\HestoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\OfferController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tymon\JWTAuth\Facades\JWTAuth;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::apiResource('locations',LocationController::class);
Route::apiResource('offers',OfferController::class);
Route::get('movies/index',[MovieController::class,'index']);
Route::post('users', [UserController::class, 'store']);
Route::post('login', [UserController::class, 'login']);


Route::middleware('jwt.auth')->group(function () {
    Route::get('user/profile', [UserController::class, 'profile']);
    Route::put('user/update', [UserController::class, 'update']);
    Route::delete('user/destroy', [UserController::class, 'destroy']);

    Route::get('histores',[HestoryController::class,'show']);
    Route::post('histores',[HestoryController::class,'store']);
    Route::delete('histores',[HestoryController::class,'destroy']);

    Route::get('movies/{id}',[MovieController::class,'show']);
    Route::post('movies/store',[MovieController::class,'store']);
    Route::delete('movies/{id}',[MovieController::class,'destroy']);
    Route::get('movies', [MovieController::class, 'moviesUser']);
});