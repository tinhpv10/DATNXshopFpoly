<?php

use App\Http\Controllers\Auth\Logout;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Client\Body;
use App\Http\Controllers\Client\GoogleController;
use App\Http\Controllers\Client\Home;
use App\Http\Controllers\Client\Profile;
use App\Http\Controllers\Gmail\OrderController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::prefix('/')->group(function () {
    Route::get('/', [HomeController::class, 'home']);

    Route::get('/search', [ProductController::class, 'search'])->name('search');
    Route::get('/product', [ProductController::class, 'index',]);
    Route::get('/products/category/{category}', [ProductController::class, 'showByCategory'])->name('products.category');
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('product.detail');
    Route::post('/add-to-cart', [ProductController::class, 'addToCart'])->name('product.addToCart');
    Route::get('/cart', [CartController::class, 'viewCart'])->name('cart.view');
    Route::post('/update-cart', [CartController::class, 'updateCart'])->name('cart.update');
    Route::delete('/cart/remove/{product_id}', [CartController::class, 'removeFromCart'])->name('cart.remove');






});
Route::post('/orders/{orderId}/update-status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
Route::post('/orders/{orderId}/update-status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
//Dashboard
Route::prefix('/dashboard')->group(function () {
    Route::get('/', function () {
        return view('index');
    });
    Route::get('/', [Home::class, 'index']);
    Route::get('/', [Body::class, 'index']);
    Route::get('/logout', [Logout::class, 'index'])->name('logout');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [Profile::class, 'index'])->name('profile');
    Route::put('/profile/update', [Profile::class, 'update'])->name('profile.update');
    Route::post('/profile/update-avatar', [Profile::class, 'updateAvatar'])->name('profile.update.avatar');
});

/// login google
Route::get('/auth/google', [GoogleController::class, 'googlepage']);
Route::get('/auth/google/callback', [GoogleController::class, 'googlecallback']);

require __DIR__ . '/auth.php';

