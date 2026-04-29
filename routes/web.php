<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'index'])->name('products.index');

Route::get('/product/{slug}', [ProductController::class, 'show'])->name('products.show');

Route::post('/product/{product}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

Route::get('/cart', function () {
    return view('products.cart');
})->name('cart.index');

Route::get('/checkout', function () {
    return view('checkout');
})->name('checkout');
