<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Http\Controllers\PaymentController;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            \App\Listeners\MergeCartOnLogin::class
        );

        // Re-register Cashier routes with our custom webhook controller
        Cashier::ignoreRoutes();

        Route::prefix(config('cashier.path', 'stripe'))
            ->name('cashier.')
            ->group(function () {
                Route::get('payment/{id}', [PaymentController::class, 'show'])->name('payment');
                Route::post('webhook', [\App\Http\Controllers\StripeWebhookController::class, 'handleWebhook'])->name('webhook');
            });
    }
}
