<?php

/*
|--------------------------------------------------------------------------
| E2E Test — Ver Pedido en Panel de Usuario
|--------------------------------------------------------------------------
*/

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
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

it('can view order detail page', function (): void {
    $user = createTestUser();
    $product = createTestProduct(['price' => 59.99]);

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

    $this->actingAs($user)->get("/orders/{$order->id}")
        ->assertOk()
        ->assertSee(Order::getStatusLabel(Order::STATUS_COMPLETED));
});

it('cannot view another users order', function (): void {
    $user = createTestUser();
    $otherUser = User::factory()->create(['role' => 'user']);
    $product = createTestProduct();

    $order = Order::factory()->create(['user_id' => $otherUser->id]);

    $this->actingAs($user)->get("/orders/{$order->id}")
        ->assertStatus(403);
});

it('can see order in profile page', function (): void {
    $user = createTestUser();
    $product = createTestProduct(['price' => 39.50]);

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

    $this->actingAs($user)->get('/profile')
        ->assertOk()
        ->assertSee("#{$order->id}");
});

it('can download invoice for own order', function (): void {
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

    $response = $this->actingAs($user)->get("/orders/{$order->id}/invoice");
    $response->assertOk();
    $response->assertHeader('content-type', 'application/pdf');
});
