<?php

use App\Http\Controllers\HestoryController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tymon\JWTAuth\Facades\JWTAuth;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');
Route::apiResource('locations',LocationController::class);
Route::apiResource('movies',MovieController::class);
Route::apiResource('histores',HestoryController::class);
Route::apiResource('users',UserController::class);
Route::post('login', [UserController::class, 'login']);

Route::middleware(['jwt.auth'])->get('/user-profile', function (Request $request) {
    return response()->json($request->user());
});