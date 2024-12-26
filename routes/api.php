<?php

use App\Http\Controllers\Api\v1\ArticlesController;
use App\Http\Controllers\Api\v1\CategoriesController;
use App\Http\Controllers\Api\v1\LoginController;
use App\Http\Controllers\Api\v1\ProfileController;
use App\Http\Controllers\Api\v1\RegisterController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('login', [LoginController::class, 'login'])->middleware('guest');
    Route::post('register', [RegisterController::class, 'register'])->middleware('guest');

    Route::get('user', [ProfileController::class, 'user'])->middleware('auth:sanctum');
    Route::put('user', [ProfileController::class, 'edit'])->middleware('auth:sanctum');

    Route::get('articles', [ArticlesController::class, 'index']);
    Route::get('articles/{article:slug}', [ArticlesController::class, 'show']);

    Route::get('categories/top', [CategoriesController::class, 'top']);
});
