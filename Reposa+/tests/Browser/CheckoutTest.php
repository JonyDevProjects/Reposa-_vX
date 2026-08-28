<?php

/*
|--------------------------------------------------------------------------
| E2E Test — Checkout Completo
|--------------------------------------------------------------------------
*/

use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Mail;

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

it('completes checkout successfully with cart items', function (): void {
    Mail::fake();
    $user = createTestUser();
    $product = createTestProduct([
        'name' => 'Almohada Viscoelástica',
        'price' => 45.99,
        'stock' => 10,
    ]);

    createCartItem($user, $product, 2);

    $response = $this->actingAs($user)->post('/checkout');
    $response->assertRedirect('/profile#orders');

    $order = Order::where('user_id', $user->id)->first();
    expect($order)->not->toBeNull();
    expect($order->status)->toBe('pending');
    expect($product->fresh()->stock)->toBe(8);
    expect(\App\Models\CartItem::where('user_id', $user->id)->count())->toBe(0);
});

it('prevents checkout with empty cart', function (): void {
    $user = createTestUser();

    $response = $this->actingAs($user)->post('/checkout');
    $response->assertSessionHas('error');
    $this->assertDatabaseMissing('orders', ['user_id' => $user->id]);
});

it('prevents checkout when stock is insufficient', function (): void {
    $user = createTestUser();
    $product = createTestProduct(['stock' => 2, 'price' => 30.00]);

    createCartItem($user, $product, 5);

    $response = $this->actingAs($user)->post('/checkout');
    $response->assertSessionHas('error');
    expect($product->fresh()->stock)->toBe(2);
});

it('redirects to login when guest tries checkout', function (): void {
    $response = $this->post('/checkout');
    $response->assertRedirect();
});

it('validates stock atomically during checkout', function (): void {
    Mail::fake();
    $user = createTestUser();
    $product = createTestProduct(['stock' => 3, 'price' => 25.00]);

    createCartItem($user, $product, 3);

    $response = $this->actingAs($user)->post('/checkout');
    $response->assertRedirect('/profile#orders');
    expect($product->fresh()->stock)->toBe(0);
});
