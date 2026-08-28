<?php

/*
|--------------------------------------------------------------------------
| E2E Test — Pago Fallido
|--------------------------------------------------------------------------
*/

use App\Models\Order;
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

it('handles payment failure webhook correctly', function (): void {
    Mail::fake();
    Http::fake();

    $user = createTestUser();
    $product = createTestProduct(['price' => 59.99, 'stock' => 10]);

    $order = Order::factory()->pending()->create([
        'user_id' => $user->id,
        'total_amount' => 59.99,
        'payment_intent_id' => 'pi_test_' . Str::random(14),
    ]);

    \App\Models\OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price_at_purchase' => 59.99,
    ]);

    $payload = [
        'id' => 'evt_test_' . Str::random(24),
        'type' => 'payment_intent.payment_failed',
        'data' => [
            'object' => [
                'id' => $order->payment_intent_id,
                'customer' => null,
                'last_payment_error' => ['message' => 'Your card was declined.'],
            ],
        ],
    ];

    test()->post('/stripe/webhook', $payload, [
        'Content-Type' => 'application/json',
    ]);

    expect($order->fresh()->status)->toBe('pending');
});

it('shows payment cancellation redirects to cart', function (): void {
    $user = createTestUser();

    $this->actingAs($user)->get('/checkout/stripe/cancel')
        ->assertRedirect('/cart');
});

it('preserves cart when payment is cancelled', function (): void {
    $user = createTestUser();
    $product = createTestProduct(['stock' => 5, 'price' => 29.99]);
    createCartItem($user, $product, 1);

    $this->actingAs($user)->get('/checkout/stripe/cancel')
        ->assertRedirect('/cart');

    expect(\App\Models\CartItem::where('user_id', $user->id)->count())->toBe(1);
    $this->assertDatabaseHas('cart_items', [
        'user_id' => $user->id,
        'product_id' => $product->id,
    ]);
});
