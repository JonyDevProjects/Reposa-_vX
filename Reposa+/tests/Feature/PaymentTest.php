<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_stripe_checkout_requires_authentication(): void
    {
        $response = $this->get('/checkout/stripe');

        $response->assertRedirect();
    }

    public function test_stripe_checkout_redirects_with_empty_cart(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/checkout/stripe');

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_stripe_success_requires_session_id(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/checkout/stripe/success');

        $response->assertRedirect('/');
    }

    public function test_stripe_cancel_redirects_to_cart(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/checkout/stripe/cancel');

        $response->assertRedirect('/cart');
        $response->assertSessionHas('error');
    }

    public function test_user_can_view_own_order(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get("/orders/{$order->id}");

        $response->assertOk();
    }

    public function test_user_cannot_view_other_user_order(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get("/orders/{$order->id}");

        $response->assertStatus(403);
    }

    public function test_user_can_download_own_invoice(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->completed()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get("/orders/{$order->id}/invoice");

        $response->assertOk();
    }

    public function test_user_cannot_download_other_user_invoice(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $order = Order::factory()->completed()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get("/orders/{$order->id}/invoice");

        $response->assertStatus(403);
    }

    public function test_order_created_with_pending_status(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->pending()->create(['user_id' => $user->id]);

        $this->assertEquals('pending', $order->status);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'pending']);
    }

    public function test_order_with_payment_intent_has_ids(): void
    {
        $order = Order::factory()->withPaymentIntent()->create();

        $this->assertNotEmpty($order->payment_intent_id);
        $this->assertNotEmpty($order->stripe_session_id);
        $this->assertStringStartsWith('pi_', $order->payment_intent_id);
    }
}
