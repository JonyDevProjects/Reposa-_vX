<?php

/*
|--------------------------------------------------------------------------
| Pest Configuration
|--------------------------------------------------------------------------
|
| Archivo de configuracion principal de Pest. Se carga automaticamente
| antes de todos los tests.
|
*/

uses(Tests\TestCase::class)->in('Feature', 'Unit');

/*
|--------------------------------------------------------------------------
| Browser Tests — Limpieza de datos de prueba
|--------------------------------------------------------------------------
|
| Los tests de browser usan la misma MySQL que el servidor Docker.
| No usamos RefreshDatabase (migrate:fresh romperia el servidor).
| Limpiamos las tablas relevantes despues de cada test.
|
*/

afterEach(function (): void {
    $browserTestClasses = [
        \Tests\Browser\AddToCartTest::class,
        \Tests\Browser\CheckoutTest::class,
        \Tests\Browser\AdminOrderTest::class,
        \Tests\Browser\FailedPaymentTest::class,
        \Tests\Browser\MailHogTest::class,
        \Tests\Browser\StripeWebhookTest::class,
        \Tests\Browser\ViewOrderUserTest::class,
        \Tests\Browser\DataSetupTest::class,
    ];

    $currentTest = get_class($this->test);
    if (in_array($currentTest, $browserTestClasses)) {
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');
        \Illuminate\Support\Facades\DB::table('cart_items')->truncate();
        \Illuminate\Support\Facades\DB::table('order_items')->truncate();
        \Illuminate\Support\Facades\DB::table('orders')->truncate();
        \Illuminate\Support\Facades\DB::table('refunds')->truncate();
        \Illuminate\Support\Facades\DB::table('favorite_product')->truncate();
        \Illuminate\Support\Facades\DB::table('addresses')->truncate();
        \Illuminate\Support\Facades\DB::table('profiles')->truncate();
        \Illuminate\Support\Facades\DB::table('users')->where('email', 'like', '%@example.com')->delete();
        \Illuminate\Support\Facades\DB::table('products')->where('name', 'like', '%Prueba%')->delete();
        \Illuminate\Support\Facades\DB::table('products')->where('name', 'like', '%Almohada%')->delete();
        \Illuminate\Support\Facades\DB::table('categories')->where('name', 'Cervical')->delete();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
});
