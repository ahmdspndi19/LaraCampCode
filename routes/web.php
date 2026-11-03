<?php

use App\Http\Controllers\Admin\CheckoutController as AdminCheckoutController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InvoiceController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('welcome');

Route::middleware('guest')->group(function () {
    Route::view('/login', 'login')->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.store');

    Route::view('/register', 'register')->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:10,1')
        ->name('register.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // success harus di atas {camp:slug}
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('success_checkout');
    Route::get('/checkout/{camp:slug}', [CheckoutController::class, 'create'])->name('checkout');
    Route::post('/checkout/{camp:slug}', [CheckoutController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('checkout.store');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/invoices/{checkout}', [InvoiceController::class, 'show'])->name('invoice');

    // Memakai class middleware langsung, jadi tidak perlu alias 'admin'
    // di bootstrap/app.php.
    Route::middleware(AdminMiddleware::class)->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminCheckoutController::class, 'index'])->name('dashboard');
        Route::patch('/checkouts/{checkout}', [AdminCheckoutController::class, 'update'])->name('checkout.update');
    });
});
