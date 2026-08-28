<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStateTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_has_all_expected_statuses(): void
    {
        $expected = ['pending', 'processing', 'shipped', 'delivered', 'completed', 'cancelled', 'refunded'];

        $this->assertEquals($expected, array_keys(Order::STATUSES));
    }

    public function test_pending_can_transition_to_processing(): void
    {
        $this->assertTrue(Order::canTransition('pending', 'processing'));
    }

    public function test_pending_can_transition_to_completed(): void
    {
        $this->assertTrue(Order::canTransition('pending', 'completed'));
    }

    public function test_pending_can_transition_to_cancelled(): void
    {
        $this->assertTrue(Order::canTransition('pending', 'cancelled'));
    }

    public function test_pending_cannot_transition_to_shipped(): void
    {
        $this->assertFalse(Order::canTransition('pending', 'shipped'));
    }

    public function test_pending_cannot_transition_to_refunded(): void
    {
        $this->assertFalse(Order::canTransition('pending', 'refunded'));
    }

    public function test_processing_can_transition_to_shipped(): void
    {
        $this->assertTrue(Order::canTransition('processing', 'shipped'));
    }

    public function test_processing_can_transition_to_cancelled(): void
    {
        $this->assertTrue(Order::canTransition('processing', 'cancelled'));
    }

    public function test_processing_cannot_transition_to_completed(): void
    {
        $this->assertFalse(Order::canTransition('processing', 'completed'));
    }

    public function test_shipped_can_transition_to_delivered(): void
    {
        $this->assertTrue(Order::canTransition('shipped', 'delivered'));
    }

    public function test_shipped_cannot_transition_to_completed(): void
    {
        $this->assertFalse(Order::canTransition('shipped', 'completed'));
    }

    public function test_delivered_can_transition_to_completed(): void
    {
        $this->assertTrue(Order::canTransition('delivered', 'completed'));
    }

    public function test_delivered_can_transition_to_refunded(): void
    {
        $this->assertTrue(Order::canTransition('delivered', 'refunded'));
    }

    public function test_completed_can_transition_to_refunded(): void
    {
        $this->assertTrue(Order::canTransition('completed', 'refunded'));
    }

    public function test_completed_only_transitions_to_refunded(): void
    {
        $transitions = Order::getAllowedTransitions('completed');
        $this->assertEquals(['refunded'], $transitions);
    }

    public function test_cancelled_is_terminal(): void
    {
        $this->assertEmpty(Order::getAllowedTransitions('cancelled'));
    }

    public function test_refunded_is_terminal(): void
    {
        $this->assertEmpty(Order::getAllowedTransitions('refunded'));
    }

    public function test_get_status_label_returns_correct_labels(): void
    {
        $this->assertEquals('Pendiente', Order::getStatusLabel('pending'));
        $this->assertEquals('Reembolsado', Order::getStatusLabel('refunded'));
        $this->assertEquals('Completado', Order::getStatusLabel('completed'));
    }

    public function test_get_status_color_returns_correct_colors(): void
    {
        $this->assertEquals('warning', Order::getStatusColor('pending'));
        $this->assertEquals('success', Order::getStatusColor('completed'));
        $this->assertEquals('danger', Order::getStatusColor('cancelled'));
        $this->assertEquals('secondary', Order::getStatusColor('refunded'));
    }

    public function test_order_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($order->user->is($user));
    }

    public function test_order_has_many_order_items(): void
    {
        $order = Order::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $order->orderItems());
    }

    public function test_order_has_many_refunds(): void
    {
        $order = Order::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $order->refunds());
    }
}
