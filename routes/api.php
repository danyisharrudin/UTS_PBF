<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);


Route::middleware('auth:api')->group(function () {
Route::apiResource('categories', CategoryController::class);
Route::apiResource('products', ProductController::class);
});


Route::get('oauth/register', [AuthController::class, 'redirectToGoogle']);
Route::get('oauth/callback', [AuthController::class, 'handleGoogleCallback']);


?>