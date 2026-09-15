<?php

/*
|--------------------------------------------------------------------------
| E2E Test — Pago Fallido
|--------------------------------------------------------------------------
*/

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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

it('handles payment failure webhook correctly', function (): void {
    Mail::fake();
    Http::fake();

    $user = createTestUser();
    $product = createTestProduct(['price' => 59.99, 'stock' => 10]);

    $order = Order::factory()->pending()->create([
        'user_id' => $user->id,
        'total_amount' => 59.99,
        'payment_intent_id' => 'pi_test_'.Str::random(14),
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price_at_purchase' => 59.99,
    ]);

    $payload = [
        'id' => 'evt_test_'.Str::random(24),
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

    expect(CartItem::where('user_id', $user->id)->count())->toBe(1);
    $this->assertDatabaseHas('cart_items', [
        'user_id' => $user->id,
        'product_id' => $product->id,
    ]);
});
