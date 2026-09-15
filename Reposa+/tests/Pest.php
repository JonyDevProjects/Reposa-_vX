<?php

use Illuminate\Support\Facades\DB;
use Tests\Browser\AddToCartTest;
use Tests\Browser\AdminOrderTest;
use Tests\Browser\CheckoutTest;
use Tests\Browser\DataSetupTest;
use Tests\Browser\FailedPaymentTest;
use Tests\Browser\MailHogTest;
use Tests\Browser\StripeWebhookTest;
use Tests\Browser\ViewOrderUserTest;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Pest Configuration
|--------------------------------------------------------------------------
|
| Archivo de configuracion principal de Pest. Se carga automaticamente
| antes de todos los tests.
|
*/

uses(TestCase::class)->in('Feature', 'Unit');

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
        AddToCartTest::class,
        CheckoutTest::class,
        AdminOrderTest::class,
        FailedPaymentTest::class,
        MailHogTest::class,
        StripeWebhookTest::class,
        ViewOrderUserTest::class,
        DataSetupTest::class,
    ];

    $currentTest = get_class($this->test);
    if (in_array($currentTest, $browserTestClasses)) {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('cart_items')->truncate();
        DB::table('order_items')->truncate();
        DB::table('orders')->truncate();
        DB::table('refunds')->truncate();
        DB::table('favorite_product')->truncate();
        DB::table('addresses')->truncate();
        DB::table('profiles')->truncate();
        DB::table('users')->where('email', 'like', '%@example.com')->delete();
        DB::table('products')->where('name', 'like', '%Prueba%')->delete();
        DB::table('products')->where('name', 'like', '%Almohada%')->delete();
        DB::table('categories')->where('name', 'Cervical')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
});
