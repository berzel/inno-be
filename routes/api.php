<?php

use App\Http\Controllers\Api\v1\ArticlesController;
use App\Http\Controllers\Api\v1\LoginController;
use App\Http\Controllers\Api\v1\RegisterController;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('login', [LoginController::class, 'login'])->middleware('guest');
    Route::post('register', [RegisterController::class, 'register'])->middleware('guest');

    Route::get('user', function (\Illuminate\Http\Request $request) {
        return response()->json(new UserResource($request->user()));
    })->middleware('auth:sanctum');

    Route::get('articles', [ArticlesController::class, 'index']);
    Route::get('articles/{article:slug}', [ArticlesController::class, 'show']);
});
