<?php

use App\Http\Controllers\Api\CartApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\CheckoutApiController;
use App\Http\Controllers\Api\HomeApiController;
use App\Http\Controllers\Api\ProductApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('api')->group(function () {
    Route::get('/home', [HomeApiController::class, 'index']);

    Route::get('/categories', [CategoryApiController::class, 'index']);
    Route::get('/categories/type/{type}', [CategoryApiController::class, 'byType']);
    Route::get('/categories/{category}', [CategoryApiController::class, 'show']);

    Route::get('/products', [ProductApiController::class, 'index']);
    Route::post('/products', [ProductApiController::class, 'store']);
    Route::get('/products/{product}', [ProductApiController::class, 'show']);
    Route::put('/products/{product}', [ProductApiController::class, 'update']);
    Route::patch('/products/{product}', [ProductApiController::class, 'update']);
    Route::post('/products/{product}', [ProductApiController::class, 'update']);
    Route::delete('/products/{product}', [ProductApiController::class, 'destroy']);

    Route::get('/cart', [CartApiController::class, 'index']);
    Route::post('/cart/{product}', [CartApiController::class, 'store']);
    Route::put('/cart', [CartApiController::class, 'update']);
    Route::delete('/cart/{product}', [CartApiController::class, 'destroy']);
    Route::delete('/cart', [CartApiController::class, 'clear']);

    Route::get('/checkout', [CheckoutApiController::class, 'index']);
    Route::post('/checkout', [CheckoutApiController::class, 'store']);
    Route::get('/checkout/success', [CheckoutApiController::class, 'success']);
});

Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');