<?php

/*
|--------------------------------------------------------------------------
| E2E Test — Ver Pedido en Panel de Usuario
|--------------------------------------------------------------------------
|
| Valida que un usuario puede:
| 1. Ver su lista de pedidos desde /profile#orders
| 2. Ver el detalle de un pedido específico desde /orders/{id}
| 3. No puede ver pedidos de otros usuarios (403)
| 4. Puede descargar la factura PDF
|
*/

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(\Tests\TestCase::class);
uses(RefreshDatabase::class);

it('can view order detail page', function (): void {
    // Arrange
    $user = createTestUser();
    $product = createTestProduct([
        'name' => 'Almohada de Gel Refrescante',
        'price' => 59.99,
        'stock' => 30,
    ]);

    $order = Order::factory()->completed()->create([
        'user_id' => $user->id,
        'total_amount' => 59.99,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price_at_purchase' => 59.99,
    ]);

    // Act — Login
    visit(APP_BASE_URL . '/login')
        ->fill('#email', TEST_USER_EMAIL)
        ->fill('#password', TEST_USER_PASSWORD)
        ->press('button[type="submit"]');

    // Navegar al detalle del pedido
    visit(APP_BASE_URL . "/orders/{$order->id}")
        ->assertSee('Almohada de Gel Refrescante')
        ->assertSee('59.99')
        ->assertSee('Completado');
});

it('cannot view another users order', function (): void {
    // Arrange
    $user = createTestUser();
    $otherUser = User::factory()->create(['role' => 'user']);
    $product = createTestProduct();

    $order = Order::factory()->create([
        'user_id' => $otherUser->id,
    ]);

    // Act — Login como usuario normal + intentar ver pedido de otro
    $response = $this->actingAs($user)->get("/orders/{$order->id}");
    $response->assertStatus(403);
});

it('can see order in profile page', function (): void {
    // Arrange
    $user = createTestUser();
    $product = createTestProduct([
        'name' => 'Almohada Cervical',
        'price' => 39.50,
    ]);

    $order = Order::factory()->completed()->create([
        'user_id' => $user->id,
        'total_amount' => 39.50,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price_at_purchase' => 39.50,
    ]);

    // Act — Login
    visit(APP_BASE_URL . '/login')
        ->fill('#email', TEST_USER_EMAIL)
        ->fill('#password', TEST_USER_PASSWORD)
        ->press('button[type="submit"]');

    // Navegar al perfil
    visit(APP_BASE_URL . '/profile')
        ->assertSee("Pedido #{$order->id}");
});

it('can download invoice for own order', function (): void {
    // Arrange
    $user = createTestUser();
    $product = createTestProduct(['price' => 45.00]);

    $order = Order::factory()->completed()->create([
        'user_id' => $user->id,
        'total_amount' => 45.00,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price_at_purchase' => 45.00,
    ]);

    // Act
    $response = $this->actingAs($user)->get("/orders/{$order->id}/invoice");
    $response->assertOk();
    $response->assertHeader('content-type', 'application/pdf');
});
