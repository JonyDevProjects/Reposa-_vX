<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\User;
use App\Services\Shipping\ShippingServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderShipmentLifecycleSyncTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected ShippingServiceInterface $shippingService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->shippingService = app(ShippingServiceInterface::class);
    }

    private function createOrderWithShipment(string $orderStatus = Order::STATUS_PROCESSING, string $shipmentStatus = Shipment::STATUS_PRE_REGISTERED): array
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => $orderStatus,
        ]);

        $shipment = $this->shippingService->createShipment($order, [
            'name' => 'Cliente Test',
            'street' => 'Calle Mayor 1',
            'city' => 'Madrid',
            'zip_code' => '28013',
            'province' => 'Madrid',
        ]);

        if ($shipmentStatus !== Shipment::STATUS_PRE_REGISTERED) {
            $shipment->update(['status' => $shipmentStatus]);
        }

        return [$order, $shipment];
    }

    public function test_advancing_shipment_to_in_transit_updates_order_to_shipped(): void
    {
        [$order, $shipment] = $this->createOrderWithShipment(Order::STATUS_PROCESSING, Shipment::STATUS_PRE_REGISTERED);

        $response = $this->actingAs($this->admin)->post("/admin/shipments/{$shipment->id}/advance");

        $response->assertRedirect();
        $this->assertEquals(Shipment::STATUS_IN_TRANSIT, $shipment->fresh()->status);
        $this->assertEquals(Order::STATUS_SHIPPED, $order->fresh()->status);
    }

    public function test_advancing_shipment_to_delivered_updates_order_to_delivered(): void
    {
        [$order, $shipment] = $this->createOrderWithShipment(Order::STATUS_SHIPPED, Shipment::STATUS_OUT_FOR_DELIVERY);

        $response = $this->actingAs($this->admin)->post("/admin/shipments/{$shipment->id}/advance");

        $response->assertRedirect();
        $this->assertEquals(Shipment::STATUS_DELIVERED, $shipment->fresh()->status);
        $this->assertEquals(Order::STATUS_DELIVERED, $order->fresh()->status);
    }

    public function test_admin_updating_order_to_shipped_automatically_moves_shipment_to_in_transit(): void
    {
        [$order, $shipment] = $this->createOrderWithShipment(Order::STATUS_PROCESSING, Shipment::STATUS_PRE_REGISTERED);

        $response = $this->actingAs($this->admin)->patch("/admin/orders/{$order->id}/status", [
            'status' => Order::STATUS_SHIPPED,
        ]);

        $response->assertRedirect();
        $this->assertEquals(Order::STATUS_SHIPPED, $order->fresh()->status);
        $this->assertEquals(Shipment::STATUS_IN_TRANSIT, $shipment->fresh()->status);
        $this->assertNotNull($shipment->fresh()->shipped_at);
    }

    public function test_admin_updating_order_to_delivered_automatically_moves_shipment_to_delivered(): void
    {
        [$order, $shipment] = $this->createOrderWithShipment(Order::STATUS_SHIPPED, Shipment::STATUS_IN_TRANSIT);

        $response = $this->actingAs($this->admin)->patch("/admin/orders/{$order->id}/status", [
            'status' => Order::STATUS_DELIVERED,
        ]);

        $response->assertRedirect();
        $this->assertEquals(Order::STATUS_DELIVERED, $order->fresh()->status);
        $this->assertEquals(Shipment::STATUS_DELIVERED, $shipment->fresh()->status);
        $this->assertNotNull($shipment->fresh()->delivered_at);
    }

    public function test_admin_updating_order_to_completed_automatically_moves_shipment_to_delivered(): void
    {
        [$order, $shipment] = $this->createOrderWithShipment(Order::STATUS_DELIVERED, Shipment::STATUS_IN_TRANSIT);

        $response = $this->actingAs($this->admin)->patch("/admin/orders/{$order->id}/status", [
            'status' => Order::STATUS_COMPLETED,
        ]);

        $response->assertRedirect();
        $this->assertEquals(Order::STATUS_COMPLETED, $order->fresh()->status);
        $this->assertEquals(Shipment::STATUS_DELIVERED, $shipment->fresh()->status);
    }

    public function test_admin_cancelling_order_automatically_cancels_shipment(): void
    {
        [$order, $shipment] = $this->createOrderWithShipment(Order::STATUS_PROCESSING, Shipment::STATUS_PRE_REGISTERED);

        $response = $this->actingAs($this->admin)->patch("/admin/orders/{$order->id}/status", [
            'status' => Order::STATUS_CANCELLED,
        ]);

        $response->assertRedirect();
        $this->assertEquals(Order::STATUS_CANCELLED, $order->fresh()->status);
        $this->assertEquals(Shipment::STATUS_CANCELLED, $shipment->fresh()->status);
    }

    public function test_admin_cannot_advance_shipment_for_terminal_or_delivered_order(): void
    {
        [$order, $shipment] = $this->createOrderWithShipment(Order::STATUS_DELIVERED, Shipment::STATUS_IN_TRANSIT);

        $response = $this->actingAs($this->admin)->post("/admin/shipments/{$shipment->id}/advance");

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_admin_cannot_advance_cancelled_shipment(): void
    {
        [$order, $shipment] = $this->createOrderWithShipment(Order::STATUS_CANCELLED, Shipment::STATUS_CANCELLED);

        $response = $this->actingAs($this->admin)->post("/admin/shipments/{$shipment->id}/advance");

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_ui_hides_advance_button_for_delivered_completed_and_cancelled_orders(): void
    {
        [$deliveredOrder] = $this->createOrderWithShipment(Order::STATUS_DELIVERED, Shipment::STATUS_DELIVERED);
        [$completedOrder] = $this->createOrderWithShipment(Order::STATUS_COMPLETED, Shipment::STATUS_DELIVERED);
        [$cancelledOrder] = $this->createOrderWithShipment(Order::STATUS_CANCELLED, Shipment::STATUS_CANCELLED);
        [$processingOrder] = $this->createOrderWithShipment(Order::STATUS_PROCESSING, Shipment::STATUS_PRE_REGISTERED);

        $response = $this->actingAs($this->admin)->get('/admin/orders');

        $response->assertOk();
        // Para processing debe existir el botón de avanzar
        $response->assertSee('Avanzar');
        // Para cancelado debe mostrarse la insignia de Anulada
        $response->assertSee('Anulada');
    }

    public function test_admin_can_refund_direct_order_via_status_update_restocks_and_creates_refund_record(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);
        $order = Order::factory()->completed()->create([
            'user_id' => $user->id,
            'payment_intent_id' => null,
            'stripe_session_id' => null,
            'total_amount' => 50.00,
        ]);
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price_at_purchase' => 25.00,
        ]);

        $response = $this->actingAs($this->admin)->patch("/admin/orders/{$order->id}/status", [
            'status' => Order::STATUS_REFUNDED,
        ]);

        $response->assertRedirect();
        $this->assertEquals(Order::STATUS_REFUNDED, $order->fresh()->status);
        $this->assertEquals(12, $product->fresh()->stock);
        $this->assertDatabaseHas('refunds', [
            'order_id' => $order->id,
            'amount' => 50.00,
            'status' => 'succeeded',
        ]);
    }

    public function test_ui_differentiates_payment_badges_and_allows_refund_option_only_for_direct_orders(): void
    {
        // Pedido Directo Completado
        Order::factory()->completed()->create([
            'payment_intent_id' => null,
            'stripe_session_id' => null,
        ]);

        // Pedido Stripe Pagado Completado
        Order::factory()->completed()->create([
            'payment_intent_id' => 'pi_test_stripe_123',
            'stripe_session_id' => 'cs_test_session_123',
        ]);

        // Pedido Stripe Abandonado / Sin Cobro
        Order::factory()->create([
            'status' => Order::STATUS_PENDING,
            'payment_intent_id' => null,
            'stripe_session_id' => 'cs_test_unpaid_123',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/orders');

        $response->assertOk();
        $response->assertSee('Directo');
        $response->assertSee('Stripe');
        $response->assertSee('Stripe (Sin cobro)');

        $content = $response->getContent();
        $this->assertStringContainsString('value="refunded"', $content);
        $this->assertStringContainsString('Reembolsar', $content);
    }
}
