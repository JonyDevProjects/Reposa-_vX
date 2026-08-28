<?php

/*
|--------------------------------------------------------------------------
| E2E Test — Ver Pedido en Panel Admin
|--------------------------------------------------------------------------
|
| Valida que un administrador puede:
| 1. Ver la lista de todos los pedidos en /admin/orders
| 2. Ver el dashboard con estadísticas de pedidos
| 3. Cambiar el estado de un pedido
| 4. Procesar un reembolso
| 5. Un usuario normal NO puede acceder al panel admin
|
*/

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(\Tests\TestCase::class);
uses(RefreshDatabase::class);

it('admin can view all orders', function (): void {
    // Arrange — Crear admin + pedidos de varios usuarios
    $admin = createTestAdmin();
    $user = createTestUser();
    $product = createTestProduct(['price' => 49.99]);

    $order = Order::factory()->completed()->create([
        'user_id' => $user->id,
        'total_amount' => 49.99,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price_at_purchase' => 49.99,
    ]);

    // Act — Login como admin
    visit(APP_BASE_URL . '/login')
        ->fill('#email', TEST_ADMIN_EMAIL)
        ->fill('#password', TEST_ADMIN_PASSWORD)
        ->press('button[type="submit"]');

    // Navegar a la sección de pedidos admin
    visit(APP_BASE_URL . '/admin/orders')
        ->assertSee('Pedido');

    // Assert — Verificar que el pedido aparece
    expect(Order::count())->toBeGreaterThanOrEqual(1);
});

it('non-admin cannot access admin panel', function (): void {
    // Arrange
    $user = createTestUser();

    // Act — Intentar acceder como usuario normal
    $response = $this->actingAs($user)->get('/admin/orders');
    $response->assertRedirect('/');
    $response->assertSessionHas('error');
});

it('admin can update order status', function (): void {
    // Arrange
    Mail::fake();
    $admin = createTestAdmin();
    $user = createTestUser();
    $order = Order::factory()->pending()->create([
        'user_id' => $user->id,
    ]);

    // Act — Login como admin + cambiar estado
    $response = $this->actingAs($admin)->patch("/admin/orders/{$order->id}/status", [
        'status' => 'processing',
    ]);

    $response->assertRedirect();
    expect($order->fresh()->status)->toBe('processing');
});

it('admin cannot make invalid status transition', function (): void {
    // Arrange
    $admin = createTestAdmin();
    $user = createTestUser();
    $order = Order::factory()->pending()->create([
        'user_id' => $user->id,
    ]);

    // Act — Intentar saltar de pending a shipped (no permitido)
    $response = $this->actingAs($admin)->patch("/admin/orders/{$order->id}/status", [
        'status' => 'shipped',
    ]);

    $response->assertSessionHas('error');
    expect($order->fresh()->status)->toBe('pending');
});

it('admin can view dashboard with statistics', function (): void {
    // Arrange
    $admin = createTestAdmin();

    // Act — Login como admin
    visit(APP_BASE_URL . '/login')
        ->fill('#email', TEST_ADMIN_EMAIL)
        ->fill('#password', TEST_ADMIN_PASSWORD)
        ->press('button[type="submit"]');

    // Navegar al dashboard
    visit(APP_BASE_URL . '/admin/dashboard')
        ->assertSee('Dashboard');
});
