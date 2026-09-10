<?php

/*
|--------------------------------------------------------------------------
| E2E Test — Ver Pedido en Panel Admin
|--------------------------------------------------------------------------
*/

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
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
});

it('admin can view all orders', function (): void {
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

    $this->actingAs($admin)->get('/admin/orders')
        ->assertOk()
        ->assertSee('Pedido');
});

it('non-admin cannot access admin panel', function (): void {
    $user = createTestUser();

    $this->actingAs($user)->get('/admin/orders')
        ->assertRedirect('/')
        ->assertSessionHas('error');
});

it('admin can update order status', function (): void {
    Mail::fake();
    $admin = createTestAdmin();
    $user = createTestUser();
    $order = Order::factory()->pending()->create(['user_id' => $user->id]);

    $this->actingAs($admin)->patch("/admin/orders/{$order->id}/status", [
        'status' => 'processing',
    ])->assertRedirect();

    expect($order->fresh()->status)->toBe('processing');
});

it('admin cannot make invalid status transition', function (): void {
    $admin = createTestAdmin();
    $user = createTestUser();
    $order = Order::factory()->pending()->create(['user_id' => $user->id]);

    $this->actingAs($admin)->patch("/admin/orders/{$order->id}/status", [
        'status' => 'shipped',
    ])->assertSessionHas('error');

    expect($order->fresh()->status)->toBe('pending');
});

it('admin can view dashboard with statistics', function (): void {
    $admin = createTestAdmin();

    $this->actingAs($admin)->get('/admin/dashboard')
        ->assertOk()
        ->assertSee('Dashboard');
});
