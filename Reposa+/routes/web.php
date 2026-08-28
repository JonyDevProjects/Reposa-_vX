<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LanguageController;

Route::get('/', [HomeController::class, 'index']);
Route::get('/catalog', [ProductController::class, 'index']);
Route::get('/catalog/{product}', [ProductController::class, 'show'])->name('products.show');

Route::get('/lang/{locale}', [LanguageController::class, 'switchLang'])->name('lang.switch');

// Rutas del Carrito (Abiertas a invitados)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/login', [CartController::class, 'requireLogin'])->name('cart.login');

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/address', [ProfileController::class, 'storeAddress'])->name('profile.address.store');
    Route::put('/profile/address/{address}', [ProfileController::class, 'updateAddress'])->name('profile.address.update');
    Route::delete('/profile/address/{address}', [ProfileController::class, 'destroyAddress'])->name('profile.address.destroy');

    // Rutas de Checkout/Pedidos (Solo usuarios autenticados)
    Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout');
    Route::get('/orders', [CartController::class, 'orders'])->name('orders.index');
    Route::get('/orders/{order}', [CartController::class, 'showOrder'])->name('orders.show');
    Route::get('/orders/{order}/invoice', [CartController::class, 'downloadInvoice'])->name('orders.invoice');
    Route::post('/favorites/{product}', [ProfileController::class, 'toggleFavorite'])->name('favorites.toggle');
    Route::delete('/favorites/{product}', [ProfileController::class, 'removeFavorite'])->name('favorites.destroy');

    // Stripe Checkout
    Route::get('/checkout/stripe', [CartController::class, 'stripeCheckout'])->name('stripe.checkout');
    Route::get('/checkout/stripe/success', [CartController::class, 'stripeSuccess'])->name('stripe.success');
    Route::get('/checkout/stripe/cancel', [CartController::class, 'stripeCancel'])->name('stripe.cancel');
});

// Stripe Webhook (fuera de auth — Stripe envía sin sesión)
Route::post('/stripe/webhook', [\App\Http\Controllers\StripeWebhookController::class, 'handleWebhook'])->name('cashier.webhook');

// Rutas de Administración
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Gestión de Productos
    Route::get('/products', [AdminController::class, 'products'])->name('admin.products');
    Route::get('/products/create', [AdminController::class, 'createProduct'])->name('admin.products.create');
    Route::post('/products', [AdminController::class, 'storeProduct'])->name('admin.products.store');
    Route::get('/products/{product}/edit', [AdminController::class, 'editProduct'])->name('admin.products.edit');
    Route::put('/products/{product}', [AdminController::class, 'updateProduct'])->name('admin.products.update');
    Route::delete('/products/{product}', [AdminController::class, 'deleteProduct'])->name('admin.products.delete');

    // Gestión de Categorías
    Route::get('/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::get('/categories/create', [AdminController::class, 'createCategory'])->name('admin.categories.create');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('admin.categories.store');
    Route::get('/categories/{category}/edit', [AdminController::class, 'editCategory'])->name('admin.categories.edit');
    Route::put('/categories/{category}', [AdminController::class, 'updateCategory'])->name('admin.categories.update');
    Route::delete('/categories/{category}', [AdminController::class, 'deleteCategory'])->name('admin.categories.delete');

    // Historial de Pedidos Global
    Route::get('/orders', [AdminController::class, 'orders'])->name('admin.orders');
    Route::patch('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('admin.orders.updateStatus');
    Route::post('/orders/{order}/refund', [AdminController::class, 'refundOrder'])->name('admin.orders.refund');
});
