<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Shipment;
use App\Models\User;
use App\Services\Shipping\MockStandardCourierService;
use App\Services\Shipping\ShippingServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ShippingServiceInterface $shippingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shippingService = app(ShippingServiceInterface::class);
    }

    public function test_shipping_service_is_bound_to_mock_courier(): void
    {
        $this->assertInstanceOf(MockStandardCourierService::class, $this->shippingService);
    }

    public function test_calculate_rates_respects_free_shipping_threshold(): void
    {
        // Under 50€ threshold
        $ratesUnder = $this->shippingService->calculateRates(39.99);
        $standardUnder = collect($ratesUnder)->firstWhere('id', 'standard_48h');
        $this->assertEquals(4.95, $standardUnder['cost']);
        $this->assertFalse($standardUnder['is_free']);

        // Over 50€ threshold
        $ratesOver = $this->shippingService->calculateRates(65.00);
        $standardOver = collect($ratesOver)->firstWhere('id', 'standard_48h');
        $this->assertEquals(0.00, $standardOver['cost']);
        $this->assertTrue($standardOver['is_free']);

        // Express should always have cost
        $express = collect($ratesOver)->firstWhere('id', 'express_24h');
        $this->assertEquals(7.95, $express['cost']);
    }

    public function test_create_shipment_generates_tracking_number_and_label(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'total_amount' => 79.90,
            'status' => Order::STATUS_PENDING,
        ]);

        $recipientData = [
            'name' => 'María García',
            'email' => 'maria@example.com',
            'phone' => '+34 600 123 456',
            'street' => 'Calle Mayor 15, 2B',
            'city' => 'Valencia',
            'zip_code' => '46001',
            'province' => 'Valencia',
            'country' => 'ES',
        ];

        $shipment = $this->shippingService->createShipment($order, $recipientData, 'standard_48h');

        $this->assertNotNull($shipment->id);
        $this->assertStringStartsWith('RPX'.date('Y'), $shipment->tracking_number);
        $this->assertStringEndsWith('ES', $shipment->tracking_number);
        $this->assertEquals(Shipment::STATUS_PRE_REGISTERED, $shipment->status);
        $this->assertEquals(0.00, $shipment->shipping_cost); // Over 50€
        $this->assertEquals('Valencia', $shipment->city);
        $this->assertNotEmpty($shipment->tracking_history);
        $this->assertNotEmpty($shipment->label_data);
        $this->assertEquals($order->id, $shipment->order_id);
    }

    public function test_get_tracking_returns_timeline(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $shipment = $this->shippingService->createShipment($order, [
            'name' => 'Carlos López',
            'street' => 'Gran Vía 28',
            'city' => 'Madrid',
            'zip_code' => '28013',
            'province' => 'Madrid',
        ]);

        $tracking = $this->shippingService->getTracking($shipment->tracking_number);

        $this->assertNotNull($tracking);
        $this->assertEquals($shipment->tracking_number, $tracking['tracking_number']);
        $this->assertEquals(Shipment::STATUS_PRE_REGISTERED, $tracking['status']);
        $this->assertCount(1, $tracking['events']);

        $nonExistent = $this->shippingService->getTracking('NON_EXISTENT_TRACKING');
        $this->assertNull($nonExistent);
    }

    public function test_advance_tracking_status_lifecycle_and_order_sync(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => Order::STATUS_PENDING,
        ]);

        $shipment = $this->shippingService->createShipment($order, [
            'name' => 'Ana Ruiz',
            'street' => 'Paseo de Gracia 10',
            'city' => 'Barcelona',
            'zip_code' => '08007',
            'province' => 'Barcelona',
        ]);

        // Advance 1: pre_registered -> in_transit
        $shipment = $this->shippingService->advanceTrackingStatus($shipment);
        $this->assertEquals(Shipment::STATUS_IN_TRANSIT, $shipment->status);
        $this->assertNotNull($shipment->shipped_at);
        $this->assertEquals(Order::STATUS_SHIPPED, $order->fresh()->status);
        $this->assertCount(2, $shipment->tracking_history);

        // Advance 2: in_transit -> at_hub
        $shipment = $this->shippingService->advanceTrackingStatus($shipment);
        $this->assertEquals(Shipment::STATUS_AT_HUB, $shipment->status);
        $this->assertCount(3, $shipment->tracking_history);

        // Advance 3: at_hub -> out_for_delivery
        $shipment = $this->shippingService->advanceTrackingStatus($shipment);
        $this->assertEquals(Shipment::STATUS_OUT_FOR_DELIVERY, $shipment->status);
        $this->assertCount(4, $shipment->tracking_history);

        // Advance 4: out_for_delivery -> delivered
        $shipment = $this->shippingService->advanceTrackingStatus($shipment);
        $this->assertEquals(Shipment::STATUS_DELIVERED, $shipment->status);
        $this->assertNotNull($shipment->delivered_at);
        $this->assertEquals(Order::STATUS_DELIVERED, $order->fresh()->status);
        $this->assertCount(5, $shipment->tracking_history);
    }

    public function test_generate_label_contains_carrier_routing_and_barcodes(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $shipment = $this->shippingService->createShipment($order, [
            'name' => 'David Morales',
            'street' => 'Calle Sierpes 4',
            'city' => 'Sevilla',
            'zip_code' => '41004',
            'province' => 'Sevilla',
        ]);

        $label = $this->shippingService->generateLabel($shipment);

        $this->assertArrayHasKey('carrier_logo', $label);
        $this->assertArrayHasKey('tracking_number', $label);
        $this->assertArrayHasKey('barcode_128', $label);
        $this->assertArrayHasKey('routing_code', $label);
        $this->assertArrayHasKey('sender', $label);
        $this->assertArrayHasKey('recipient', $label);
        $this->assertArrayHasKey('package', $label);
        $this->assertEquals('David Morales', $label['recipient']['name']);
        $this->assertStringContainsString('41004', $label['routing_code']);
    }
}
