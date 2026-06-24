<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CheckoutStockTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_decrements_stock_correctly()
    {
        Mail::fake();

        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'price' => 20.00]);

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $response = $this->actingAs($user)->post('/checkout');

        $response->assertRedirect('/profile#orders');
        $this->assertEquals(7, $product->fresh()->stock);
        $this->assertDatabaseHas('orders', ['user_id' => $user->id, 'status' => 'pending']);
        $this->assertDatabaseHas('order_items', ['product_id' => $product->id, 'quantity' => 3]);
        $this->assertDatabaseMissing('cart_items', ['user_id' => $user->id]);
    }

    public function test_checkout_fails_and_rolls_back_when_stock_is_insufficient()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 2, 'price' => 20.00]);

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        $response = $this->actingAs($user)->post('/checkout');

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertEquals(2, $product->fresh()->stock);
        $this->assertDatabaseMissing('orders', ['user_id' => $user->id]);
        $this->assertDatabaseHas('cart_items', ['user_id' => $user->id]);
    }
}
