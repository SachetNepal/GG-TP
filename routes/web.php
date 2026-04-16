<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/contact', function () {
    return view('contact');
})->name('contact');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/cart', function () {
    return view('cart.index');
})->name('cart');

// ----------------------------
// Phase 1: Auth / Catalog / Checkout
// ----------------------------
Route::get('/register-type', function () {
    return view('auth.register-type');
})->name('register-type');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::get('/categories', function () {
    return view('categories.index');
})->name('categories');

Route::get('/products/{id}', function ($id) {
    return view('products.show', ['productId' => $id]);
})->name('products.show');

Route::get('/checkout/collection-slot', function () {
    return view('checkout.collection-slot');
})->name('checkout.collection-slot');

// ----------------------------
// Phase 2: Customer Profile / Contact / Traders
// ----------------------------
Route::get('/profile', function () {
    return view('profile.index');
})->name('profile.index');

Route::get('/shops', function () {
    return view('traders.index');
})->name('shops.index');

Route::get('/traders', function () {
    return redirect('/shops');
})->name('traders.index');
