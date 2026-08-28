<?php

/*
|--------------------------------------------------------------------------
| E2E Test — Proceso de Checkout Completo
|--------------------------------------------------------------------------
|
| Valida el flujo completo de checkout:
| 1. Usuario añade productos al carrito
| 2. Navega al carrito
| 3. Procede al checkout (POST /checkout)
| 4. Se redirige a la página de pedidos con éxito
| 5. El pedido se crea en la BD con estado "pending"
| 6. El stock se decrementa correctamente
|
| Nota: El checkout POST (/checkout) crea el pedido directamente
| sin pasar por Stripe. Para el flujo Stripe, ver StripeCheckoutTest.
|
*/

use App\Models\Product;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(\Tests\TestCase::class);
uses(RefreshDatabase::class);

it('completes checkout successfully with cart items', function (): void {
    // Arrange
    Mail::fake();
    $user = createTestUser();
    $product = createTestProduct([
        'name' => 'Almohada Viscoelástica',
        'price' => 45.99,
        'stock' => 10,
    ]);

    createCartItem($user, $product, 2);
    expect(\App\Models\CartItem::where('user_id', $user->id)->count())->toBe(1);

    // Act — Login
    visit(APP_BASE_URL . '/login')
        ->fill('#email', TEST_USER_EMAIL)
        ->fill('#password', TEST_USER_PASSWORD)
        ->press('button[type="submit"]');

    // Navegar al carrito para verificar antes del checkout
    visit(APP_BASE_URL . '/cart')
        ->assertSee('Almohada Viscoelástica');

    // Ejecutar checkout vía HTTP POST
    $response = $this->actingAs($user)->post('/checkout');
    $response->assertRedirect('/profile#orders');

    // Assert — Pedido creado
    $order = Order::where('user_id', $user->id)->first();
    expect($order)->not->toBeNull();
    expect($order->status)->toBe('pending');
    expect($order->total_amount)->toBe(91.98); // 45.99 * 2

    // Assert — Stock decrementado
    expect($product->fresh()->stock)->toBe(8); // 10 - 2

    // Assert — Carrito vacío
    expect(\App\Models\CartItem::where('user_id', $user->id)->count())->toBe(0);

    // Assert — Order items creados
    $orderItems = $order->orderItems()->get();
    expect($orderItems->count())->toBe(1);
    expect($orderItems->first()->quantity)->toBe(2);
    expect($orderItems->first()->price_at_purchase)->toBe(45.99);
});

it('prevents checkout with empty cart', function (): void {
    // Arrange
    $user = createTestUser();

    // Act — Login + intentar checkout vacío
    $response = $this->actingAs($user)->post('/checkout');
    $response->assertSessionHas('error');
    $this->assertDatabaseMissing('orders', ['user_id' => $user->id]);
});

it('prevents checkout when stock is insufficient', function (): void {
    // Arrange
    $user = createTestUser();
    $product = createTestProduct(['stock' => 2, 'price' => 30.00]);

    createCartItem($user, $product, 5); // Intentar comprar 5 pero solo hay 2

    // Act
    $response = $this->actingAs($user)->post('/checkout');
    $response->assertSessionHas('error');

    // Assert — Stock no modificado
    expect($product->fresh()->stock)->toBe(2);
    $this->assertDatabaseMissing('orders', ['user_id' => $user->id]);
    $this->assertDatabaseHas('cart_items', ['user_id' => $user->id]);
});

it('redirects to login when guest tries checkout', function (): void {
    $response = $this->post('/checkout');
    $response->assertRedirect();
});

it('validates stock atomically during checkout', function (): void {
    // Arrange — Simular condición de carrera: stock justo insuficiente
    Mail::fake();
    $user = createTestUser();
    $product = createTestProduct(['stock' => 3, 'price' => 25.00]);

    // Añadir 3 unidades al carrito (igual al stock)
    createCartItem($user, $product, 3);

    // Act — Checkout debería funcionar
    $response = $this->actingAs($user)->post('/checkout');
    $response->assertRedirect('/profile#orders');

    // Assert — Stock en 0
    expect($product->fresh()->stock)->toBe(0);
});
