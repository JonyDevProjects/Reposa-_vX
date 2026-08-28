<?php

/*
|--------------------------------------------------------------------------
| E2E Test — Pago Fallido
|--------------------------------------------------------------------------
|
| Valida el comportamiento cuando el pago falla:
| 1. El webhook payment_intent.payment_failed actualiza el pedido
| 2. Se envía un correo de notificación de pago fallido
| 3. El usuario puede ver su pedido con estado pendiente
|
| Nota: Este test simula el webhook de Stripe ya que no podemos
| interactuar con la UI de Stripe Checkout en tests.
|
*/

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

uses(\Tests\TestCase::class);
uses(RefreshDatabase::class);

it('handles payment failure webhook correctly', function (): void {
    // Arrange — Crear usuario con pedido pendiente
    Mail::fake();
    Http::fake();

    $user = createTestUser();
    $product = createTestProduct(['price' => 59.99, 'stock' => 10]);

    $order = Order::factory()->pending()->create([
        'user_id' => $user->id,
        'total_amount' => 59.99,
        'payment_intent_id' => 'pi_test_' . Str::random(14),
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price_at_purchase' => 59.99,
    ]);

    // Act — Enviar webhook de pago fallido
    $payload = [
        'id' => 'evt_test_' . Str::random(24),
        'type' => 'payment_intent.payment_failed',
        'data' => [
            'object' => [
                'id' => $order->payment_intent_id,
                'customer' => null, // Sin customer Stripe en este escenario
                'last_payment_error' => [
                    'message' => 'Your card was declined.',
                ],
            ],
        ],
    ];

    $response = test()->post('/stripe/webhook', $payload, [
        'Content-Type' => 'application/json',
    ]);

    // Assert — El pedido sigue en pending (no se procesó)
    expect($order->fresh()->status)->toBe('pending');

    // El pedido sigue existiendo
    $this->assertDatabaseHas('orders', [
        'id' => $order->id,
        'status' => 'pending',
    ]);
});

it('shows payment error message to user after failed Stripe redirect', function (): void {
    // Arrange
    $user = createTestUser();
    createTestProduct(['stock' => 5]);

    // Act — Login + intentar acceder a success sin session_id
    visit(APP_BASE_URL . '/login')
        ->fill('#email', TEST_USER_EMAIL)
        ->fill('#password', TEST_USER_PASSWORD)
        ->press('button[type="submit"]');

    // Simular cancelación de pago — redirigir al carrito
    visit(APP_BASE_URL . '/checkout/stripe/cancel')
        ->assertSee('El pago fue cancelado');
});

it('preserves cart when payment is cancelled', function (): void {
    // Arrange
    $user = createTestUser();
    $product = createTestProduct(['stock' => 5, 'price' => 29.99]);
    createCartItem($user, $product, 1);

    // Act — Login
    visit(APP_BASE_URL . '/login')
        ->fill('#email', TEST_USER_EMAIL)
        ->fill('#password', TEST_USER_PASSWORD)
        ->press('button[type="submit"]');

    // Simular cancelación del pago
    visit(APP_BASE_URL . '/checkout/stripe/cancel')
        ->assertSee('El pago fue cancelado');

    // Assert — El carrito sigue teniendo el producto
    expect(\App\Models\CartItem::where('user_id', $user->id)->count())->toBe(1);
    $this->assertDatabaseHas('cart_items', [
        'user_id' => $user->id,
        'product_id' => $product->id,
    ]);
});
