<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_cart(): void
    {
        $response = $this->get('/cart');

        $response->assertOk();
    }

    public function test_user_can_add_product_to_cart(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $response = $this->actingAs($user)->post("/cart/add/{$product->id}");

        $response->assertRedirect();
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);
    }

    public function test_add_to_cart_increments_quantity(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $this->actingAs($user)->post("/cart/add/{$product->id}");
        $this->actingAs($user)->post("/cart/add/{$product->id}");

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_cannot_add_to_cart_exceeding_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 1]);

        $response = $this->actingAs($user)->post("/cart/add/{$product->id}", ['quantity' => 5]);

        $response->assertSessionHas('error');
    }

    public function test_cannot_add_out_of_stock_product(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 0]);

        $response = $this->actingAs($user)->post("/cart/add/{$product->id}");

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_user_can_update_cart_item_quantity(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);
        $cartItem = CartItem::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user)->post("/cart/update/{$cartItem->id}", ['quantity' => 3]);

        $response->assertRedirect();
        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 3,
        ]);
    }

    public function test_user_can_remove_cart_item(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);
        $cartItem = CartItem::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $response = $this->actingAs($user)->delete("/cart/remove/{$cartItem->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id]);
    }

    public function test_empty_cart_cannot_checkout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/checkout');

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('orders', ['user_id' => $user->id]);
    }

    public function test_checkout_creates_order_and_clears_cart(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'price' => 25.00]);

        CartItem::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($user)->post('/checkout');

        $response->assertRedirect('/profile#orders');
        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'total_amount' => 50.00,
            'status' => 'pending',
        ]);
        $this->assertDatabaseMissing('cart_items', ['user_id' => $user->id]);
        $this->assertEquals(8, $product->fresh()->stock);
    }
}
