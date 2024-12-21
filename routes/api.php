<?php

use App\Http\Controllers\Api\v1\LoginController;
use App\Http\Controllers\Api\v1\RegisterController;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::post('login', [LoginController::class, 'login']);
        Route::post('register', [RegisterController::class, 'register']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('user', function (\Illuminate\Http\Request $request) {
           return response()->json(new UserResource($request->user()));
        });
    });
});
