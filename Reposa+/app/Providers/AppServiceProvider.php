<?php

namespace App\Providers;

use App\Http\Controllers\StripeWebhookController;
use App\Listeners\MergeCartOnLogin;
use App\Services\Shipping\MockStandardCourierService;
use App\Services\Shipping\ShippingServiceInterface;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
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
        $this->app->bind(
            ShippingServiceInterface::class,
            MockStandardCourierService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (
            request()->header('x-forwarded-proto') === 'https'
            || str_starts_with((string) config('app.url'), 'https://')
            || (! app()->environment('testing') && ! in_array(request()->getHost(), ['localhost', '127.0.0.1']))
        ) {
            URL::forceScheme('https');
        }

        Event::listen(
            Login::class,
            MergeCartOnLogin::class
        );

        // Re-register Cashier routes with our custom webhook controller
        Cashier::ignoreRoutes();

        Route::prefix(config('cashier.path', 'stripe'))
            ->name('cashier.')
            ->group(function () {
                Route::get('payment/{id}', [PaymentController::class, 'show'])->name('payment');
                Route::post('webhook', [StripeWebhookController::class, 'handleWebhook'])->name('webhook');
            });
    }
}
