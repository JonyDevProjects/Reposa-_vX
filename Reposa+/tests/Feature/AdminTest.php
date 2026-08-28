<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Refund;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_guest_cannot_access_admin(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect();
    }

    public function test_non_admin_cannot_access_admin(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get('/admin/dashboard');

        $response->assertRedirect('/');
        $response->assertSessionHas('error');
    }

    public function test_admin_can_view_orders(): void
    {
        Order::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get('/admin/orders');

        $response->assertOk();
    }

    public function test_admin_can_update_order_status(): void
    {
        $order = Order::factory()->pending()->create();

        $response = $this->actingAs($this->admin)->patch("/admin/orders/{$order->id}/status", [
            'status' => 'processing',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'processing']);
    }

    public function test_admin_cannot_make_invalid_status_transition(): void
    {
        $order = Order::factory()->pending()->create();

        $response = $this->actingAs($this->admin)->patch("/admin/orders/{$order->id}/status", [
            'status' => 'shipped',
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'pending']);
    }

    public function test_admin_can_refund_completed_order(): void
    {
        Mail::fake();

        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 5]);
        $order = Order::factory()->completed()->withPaymentIntent()->create([
            'user_id' => $user->id,
            'total_amount' => 100.00,
        ]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price_at_purchase' => 50.00,
        ]);

        $product->decrement('stock', 2);

        Refund::create([
            'order_id' => $order->id,
            'amount' => 100.00,
            'reason' => 'Producto defectuoso',
            'stripe_refund_id' => 're_test_123',
            'status' => 'succeeded',
        ]);

        $order->update(['status' => Order::STATUS_REFUNDED]);

        foreach ($order->orderItems as $item) {
            $item->product->increment('stock', $item->quantity);
        }

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'refunded']);
        $this->assertDatabaseHas('refunds', [
            'order_id' => $order->id,
            'amount' => 100.00,
            'reason' => 'Producto defectuoso',
        ]);
        $this->assertEquals(5, $product->fresh()->stock);
    }

    public function test_cannot_refund_pending_order(): void
    {
        $order = Order::factory()->pending()->create();

        $response = $this->actingAs($this->admin)->post("/admin/orders/{$order->id}/refund");

        $response->assertSessionHas('error');
    }

    public function test_cannot_refund_order_without_payment_intent(): void
    {
        $order = Order::factory()->completed()->create([
            'payment_intent_id' => null,
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/orders/{$order->id}/refund");

        $response->assertSessionHas('error');
    }

    public function test_admin_can_create_product(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)->post('/admin/products', [
            'name' => 'Almohada Test',
            'description' => 'Descripción de prueba',
            'price' => 49.99,
            'stock' => 15,
            'categories' => [$category->id],
        ]);

        $response->assertRedirect('/admin/products');
        $this->assertDatabaseHas('products', ['name' => 'Almohada Test', 'price' => 49.99]);
    }

    public function test_admin_can_update_product(): void
    {
        $product = Product::factory()->create(['name' => 'Original']);

        $response = $this->actingAs($this->admin)->put("/admin/products/{$product->id}", [
            'name' => 'Actualizado',
            'description' => $product->description,
            'price' => $product->price,
            'stock' => $product->stock,
        ]);

        $response->assertRedirect('/admin/products');
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Actualizado']);
    }

    public function test_admin_can_delete_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)->delete("/admin/products/{$product->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_admin_can_create_category(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/categories', [
            'name' => 'Nueva Categoría',
        ]);

        $response->assertRedirect('/admin/categories');
        $this->assertDatabaseHas('categories', ['name' => 'Nueva Categoría']);
    }

    public function test_admin_can_update_category(): void
    {
        $category = Category::factory()->create(['name' => 'Original']);

        $response = $this->actingAs($this->admin)->put("/admin/categories/{$category->id}", [
            'name' => 'Actualizada',
        ]);

        $response->assertRedirect('/admin/categories');
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Actualizada']);
    }

    public function test_admin_can_delete_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->admin)->delete("/admin/categories/{$category->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}
