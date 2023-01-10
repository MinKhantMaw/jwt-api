<?php

use App\Helpers\apiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register')->name('register');
    Route::post('login', 'login')->name('login');
    Route::post('logout', 'logout')->name('logout');
});



Route::middleware('auth:api')->group(function () {
    // profile
    Route::get('profile', [ProfileController::class, 'profile'])->name('profile');
    // category
    Route::get('categories', [CategoryController::class, 'index'])->name('categories');
    // posts
    Route::post('post/create', [PostController::class, 'create'])->name('post-create');
});
