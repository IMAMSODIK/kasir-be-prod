<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\KategoriMenuController;
use App\Http\Controllers\MejaController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatistikController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    try {
        $data = [
            'kategori' => \App\Models\KategoriMenu::with('menus')->get(),
        ];
        return view('welcome', $data);
    } catch (\Exception $e) {
        return view('welcome', ['kategori' => []]);
    }
});
Route::get('/daftar-menu/load-data', [MenuController::class, 'loadData']);

Route::post('/checkout', [OrderController::class, 'checkout']);
Route::get('/order/status/{orderId}', [OrderController::class, 'checkStatus']);

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/data', [UserController::class, 'data']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::post('/users/store', [UserController::class, 'store']);
    Route::post('/users/update/{id}', [UserController::class, 'update']);
    Route::post('/users/delete/{id}', [UserController::class, 'deactivate']);
    Route::post('/users/restore/{id}', [UserController::class, 'restore']);
    Route::delete('/users/destroy/{id}', [UserController::class, 'destroy']);

    Route::get('/daftar-order', [OrderController::class, 'index']);
    Route::get('/orders/pending', [OrderController::class, 'pending']);
    Route::get('/orders/done', [OrderController::class, 'done']);
    Route::post('/orders/{id}/selesai', [OrderController::class, 'selesai']);
    Route::post('/orders/{id}/batalkan', [OrderController::class, 'batalkan']);
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);

    Route::get('/meja', [MejaController::class, 'index']);
    Route::get('/meja/data', [MejaController::class, 'data']);
    Route::get('/meja/download-qrcode', [MejaController::class, 'download']);
    Route::get('/meja/{id}', [MejaController::class, 'show']);
    Route::post('/meja/store', [MejaController::class, 'store']);
    Route::post('/meja/update/{id}', [MejaController::class, 'update']);
    Route::post('/meja/delete/{id}', [MejaController::class, 'deactivate']);
    Route::post('/meja/restore/{id}', [MejaController::class, 'restore']);
    Route::delete('/meja/destroy/{id}', [MejaController::class, 'destroy']);

    Route::get('/kategori-menu', [KategoriMenuController::class, 'index']);
    Route::get('/kategori-menu/data', [KategoriMenuController::class, 'data']);
    Route::get('/kategori-menu/{id}', [KategoriMenuController::class, 'show']);
    Route::post('/kategori-menu/store', [KategoriMenuController::class, 'store']);
    Route::post('/kategori-menu/update/{id}', [KategoriMenuController::class, 'update']);
    Route::post('/kategori-menu/delete/{id}', [KategoriMenuController::class, 'deactivate']);
    Route::post('/kategori-menu/restore/{id}', [KategoriMenuController::class, 'restore']);
    Route::delete('/kategori-menu/destroy/{id}', [KategoriMenuController::class, 'destroy']);

    Route::get('/daftar-menu', [MenuController::class, 'index']);
    Route::get('/daftar-menu/data', [MenuController::class, 'data']);
    Route::get('/daftar-menu/data-table', [MenuController::class, 'dataTable']);
    Route::post('/daftar-menu/toggle-ready', [MenuController::class, 'toggleReady']);
    Route::get('/daftar-menu/{id}', [MenuController::class, 'show']);
    Route::post('/daftar-menu/store', [MenuController::class, 'store']);
    Route::post('/daftar-menu/update', [MenuController::class, 'update']);
    Route::post('/daftar-menu/delete/{id}', [MenuController::class, 'deactivate']);
    Route::post('/daftar-menu/restore/{id}', [MenuController::class, 'restore']);
    Route::delete('/daftar-menu/destroy/{id}', [MenuController::class, 'destroy']);

    Route::get('/statistik-penjualan', [StatistikController::class, 'index']);
    Route::get('/statistik-penjualan/data', [StatistikController::class, 'data']);
    Route::get('/statistik-penjualan/download-qrcode', [StatistikController::class, 'download']);
    Route::get('/statistik-penjualan/{id}', [StatistikController::class, 'show']);
    Route::post('/statistik-penjualan/store', [StatistikController::class, 'store']);
    Route::post('/statistik-penjualan/update/{id}', [StatistikController::class, 'update']);
    Route::post('/statistik-penjualan/delete/{id}', [StatistikController::class, 'deactivate']);
    Route::post('/statistik-penjualan/restore/{id}', [StatistikController::class, 'restore']);
    Route::delete('/statistik-penjualan/destroy/{id}', [StatistikController::class, 'destroy']);

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginCheck']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');

    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index']);
    Route::post('/profile', [ProfileController::class, 'update']);
    Route::post('/change-password', [ProfileController::class, 'changePassword'])->name('password.update');

    Route::redirect('/toefl', '/coming-soon');

    Route::get('/coming-soon', function () {
        return view('pages.coming_soon');
    });
});
