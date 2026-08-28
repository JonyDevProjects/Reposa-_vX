<?php

/*
|--------------------------------------------------------------------------
| E2E Test — Simulación de Webhooks de Stripe
|--------------------------------------------------------------------------
|
| Valida el manejo de webhooks de Stripe:
| 1. checkout.session.completed — Confirma el pedido
| 2. payment_intent.payment_failed — Registra fallo
| 3. charge.refunded — Procesa reembolso
|
| Estos tests usan el endpoint POST /stripe/webhook directamente
| ya que los webhooks de Stripe se reciben como HTTP POST con
| un header de firma específico.
|
*/

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Refund;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

uses(\Tests\TestCase::class);

beforeEach(function (): void {
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
});


it('handles checkout.session.completed webhook', function (): void {
    // Arrange
    Mail::fake();
    Http::fake();

    $user = createTestUser();
    $product = createTestProduct(['price' => 75.00, 'stock' => 10]);

    $order = Order::factory()->pending()->create([
        'user_id' => $user->id,
        'total_amount' => 75.00,
        'stripe_session_id' => 'cs_test_session_123',
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price_at_purchase' => 75.00,
    ]);

    // Verificar estado inicial
    expect($order->status)->toBe('pending');

    // Act — Enviar webhook directamente al controller
    $controller = app(\App\Http\Controllers\StripeWebhookController::class);
    $payload = [
        'id' => 'evt_test_' . Str::random(24),
        'type' => 'checkout.session.completed',
        'data' => [
            'object' => [
                'id' => 'cs_test_session_123',
                'payment_status' => 'paid',
                'metadata' => ['order_id' => $order->id],
            ],
        ],
    ];

    // Crear request mock
    $request = \Illuminate\Http\Request::create(
        '/stripe/webhook',
        'POST',
        [],
        [],
        [],
        ['CONTENT_TYPE' => 'application/json'],
        json_encode($payload)
    );

    $response = $controller->handleWebhook($request);

    // Assert — Pedido completado
    $order->refresh();
    expect($order->status)->toBe('completed');

    // Assert — Stock decrementado
    expect($product->fresh()->stock)->toBe(9);
});

it('ignores checkout.session.completed for already completed order', function (): void {
    // Arrange
    Mail::fake();
    Http::fake();

    $user = createTestUser();
    $product = createTestProduct(['price' => 49.99, 'stock' => 5]);

    $order = Order::factory()->completed()->create([
        'user_id' => $user->id,
        'total_amount' => 49.99,
    ]);

    $initialStock = $product->fresh()->stock;

    // Act — Enviar webhook duplicado
    $payload = [
        'id' => 'evt_test_' . Str::random(24),
        'type' => 'checkout.session.completed',
        'data' => [
            'object' => [
                'id' => 'cs_test_duplicate',
                'payment_status' => 'paid',
                'metadata' => ['order_id' => $order->id],
            ],
        ],
    ];

    test()->post('/stripe/webhook', $payload, [
        'Content-Type' => 'application/json',
    ]);

    // Assert — Stock no se decrementa dos veces
    expect($product->fresh()->stock)->toBe($initialStock);
});

it('handles charge.refunded webhook', function (): void {
    // Arrange
    Mail::fake();
    Http::fake();

    $user = createTestUser();
    $product = createTestProduct(['price' => 60.00, 'stock' => 5]);

    $order = Order::factory()->completed()->withPaymentIntent()->create([
        'user_id' => $user->id,
        'total_amount' => 60.00,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price_at_purchase' => 60.00,
    ]);

    $product->decrement('stock', 1);

    // Act — Enviar webhook directamente al controller
    $controller = app(\App\Http\Controllers\StripeWebhookController::class);
    $chargeId = 'ch_test_' . Str::random(14);
    $payload = [
        'id' => 'evt_test_' . Str::random(24),
        'type' => 'charge.refunded',
        'data' => [
            'object' => [
                'id' => $chargeId,
                'payment_intent' => $order->payment_intent_id,
                'amount_refunded' => 6000,
            ],
        ],
    ];

    $request = \Illuminate\Http\Request::create(
        '/stripe/webhook',
        'POST',
        [],
        [],
        [],
        ['CONTENT_TYPE' => 'application/json'],
        json_encode($payload)
    );

    $controller->handleWebhook($request);

    // Assert — Pedido reembolsado
    $order->refresh();
    expect($order->status)->toBe('refunded');

    // Assert — Stock restaurado
    expect($product->fresh()->stock)->toBe(5);

    // Assert — Registro de reembolso creado
    $this->assertDatabaseHas('refunds', [
        'order_id' => $order->id,
        'stripe_refund_id' => $chargeId,
    ]);
});

it('handles payment_intent.payment_failed webhook', function (): void {
    // Arrange
    Mail::fake();
    Http::fake();

    $user = createTestUser();
    $product = createTestProduct(['price' => 89.99, 'stock' => 10]);

    $order = Order::factory()->pending()->create([
        'user_id' => $user->id,
        'total_amount' => 89.99,
        'payment_intent_id' => 'pi_test_failed_payment',
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price_at_purchase' => 89.99,
    ]);

    // Act — Enviar webhook de pago fallido
    $payload = [
        'id' => 'evt_test_' . Str::random(24),
        'type' => 'payment_intent.payment_failed',
        'data' => [
            'object' => [
                'id' => 'pi_test_failed_payment',
                'customer' => null,
                'last_payment_error' => [
                    'message' => 'Your card was declined.',
                ],
            ],
        ],
    ];

    test()->post('/stripe/webhook', $payload, [
        'Content-Type' => 'application/json',
    ]);

    // Assert — El pedido sigue en pending (no se modifica)
    expect($order->fresh()->status)->toBe('pending');
});
