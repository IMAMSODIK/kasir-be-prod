<?php

use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\ApiMenuController;
use App\Http\Controllers\ApiOrderController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\MidtransController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::post('/midtrans/callback', [MidtransController::class, 'callback']);

Route::post('/login', [ApiAuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [ApiAuthController::class, 'me']);
    Route::post('/update-profile', [ApiAuthController::class, 'updateProfile']);
    Route::post('/change-password-api', [ApiAuthController::class, 'changePassword']);
    Route::post('/logout', [ApiAuthController::class, 'logout']);

    Route::get('/menus', [ApiMenuController::class, 'index']);
    Route::get('/menus/search', [ApiMenuController::class, 'searchMenus']);

    Route::post('/checkout', [ApiOrderController::class, 'checkout']);
    Route::get('/order/status/{orderId}', [ApiOrderController::class, 'checkStatus']);
    Route::post('/orders/{id}/update-status', [ApiOrderController::class, 'updateStatus']);
    Route::post('/menus/{id}/toggle-ready', [MenuController::class, 'toggleReady']
);

    Route::get('/orders', [OrderController::class, 'indexApi']);
});