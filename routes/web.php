<?php

use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\CartWebController;
use App\Http\Controllers\Web\CatalogWebController;
use App\Http\Controllers\Web\CheckoutWebController;
use App\Http\Controllers\Web\OrderWebController;
use App\Http\Controllers\Web\ProfileWebController;
use App\Http\Controllers\Web\ReviewWebController;
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

Route::get('/login', [AuthWebController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthWebController::class, 'login']);
Route::get('/register-type', function () {
    return view('auth.register-type');
})->name('register-type');
Route::get('/register', [AuthWebController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthWebController::class, 'register']);
Route::get('/verify-email', [AuthWebController::class, 'showVerifyEmail'])->name('verify-email');
Route::post('/verify-email', [AuthWebController::class, 'verifyEmail'])->name('verify-email.submit');
Route::post('/verify-email/resend', [AuthWebController::class, 'resendVerification'])->name('verify-email.resend');
Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');

Route::get('/categories', [CatalogWebController::class, 'categories'])->name('categories');
Route::get('/products/{id}', [CatalogWebController::class, 'show'])->name('products.show');
Route::get('/shops', [CatalogWebController::class, 'shops'])->name('shops.index');
Route::redirect('/traders', '/shops');

Route::middleware('auth')->group(function (): void {
    Route::get('/cart', [CartWebController::class, 'index'])->name('cart');
    Route::post('/cart/items', [CartWebController::class, 'addItem'])->name('cart.add');
    Route::get('/checkout/collection-slot', [CheckoutWebController::class, 'showSlotPicker'])->name('checkout.collection-slot');
    Route::post('/checkout/paypal/create-order', [CheckoutWebController::class, 'createPayPalOrder'])->name('checkout.paypal.create');
    Route::post('/checkout/paypal/redirect', [CheckoutWebController::class, 'startPayPalRedirect'])->name('checkout.paypal.redirect');
    Route::get('/checkout/paypal/return', [CheckoutWebController::class, 'paypalReturn'])->name('checkout.paypal.return');
    Route::get('/checkout/paypal/cancel', [CheckoutWebController::class, 'paypalCancel'])->name('checkout.paypal.cancel');
    Route::post('/checkout/paypal/capture', [CheckoutWebController::class, 'capturePayPal'])->name('checkout.paypal.capture');
    Route::post('/checkout', [CheckoutWebController::class, 'checkout'])->name('checkout');
    Route::get('/profile', [ProfileWebController::class, 'index'])->name('profile.index');
    Route::get('/orders', [OrderWebController::class, 'index'])->name('orders.index');
    Route::post('/products/{id}/reviews', [ReviewWebController::class, 'store'])->name('products.reviews.store');
});

Route::redirect('/trader-portal', '/GG-TP/trader-portal/login.php', 302);
Route::redirect('/trader-portal/', '/GG-TP/trader-portal/login.php', 302);
