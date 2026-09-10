<?php

namespace Tests\Unit;

use App\Models\Order;
use PHPUnit\Framework\TestCase;

/**
 * Suite de pruebas unitarias puras para el autómata de estados finitos del pedido.
 * Ejecución 100% en memoria sin interacción con base de datos ni contenedor Laravel.
 */
class OrderStateUnitTest extends TestCase
{
    /**
     * El estado 'pending' solo puede transicionar a 'processing', 'completed' o 'cancelled'.
     */
    public function test_pending_valid_transitions(): void
    {
        $allowed = Order::getAllowedTransitions(Order::STATUS_PENDING);

        $this->assertEqualsCanonicalizing(
            [Order::STATUS_PROCESSING, Order::STATUS_COMPLETED, Order::STATUS_CANCELLED],
            $allowed
        );

        $this->assertTrue(Order::canTransition(Order::STATUS_PENDING, Order::STATUS_PROCESSING));
        $this->assertTrue(Order::canTransition(Order::STATUS_PENDING, Order::STATUS_COMPLETED));
        $this->assertTrue(Order::canTransition(Order::STATUS_PENDING, Order::STATUS_CANCELLED));

        // Prohibido saltar a estados logísticos avanzados o reembolsos
        $this->assertFalse(Order::canTransition(Order::STATUS_PENDING, Order::STATUS_SHIPPED));
        $this->assertFalse(Order::canTransition(Order::STATUS_PENDING, Order::STATUS_DELIVERED));
        $this->assertFalse(Order::canTransition(Order::STATUS_PENDING, Order::STATUS_REFUNDED));
    }

    /**
     * El estado 'processing' no puede saltar a 'completed' sin pasar por 'shipped'.
     */
    public function test_processing_cannot_skip_to_completed(): void
    {
        $allowed = Order::getAllowedTransitions(Order::STATUS_PROCESSING);

        $this->assertEqualsCanonicalizing(
            [Order::STATUS_SHIPPED, Order::STATUS_CANCELLED],
            $allowed
        );

        $this->assertTrue(Order::canTransition(Order::STATUS_PROCESSING, Order::STATUS_SHIPPED));
        $this->assertTrue(Order::canTransition(Order::STATUS_PROCESSING, Order::STATUS_CANCELLED));

        // Verificación crítica: no se permite bypass logístico directo a completado
        $this->assertFalse(Order::canTransition(Order::STATUS_PROCESSING, Order::STATUS_COMPLETED));
        $this->assertFalse(Order::canTransition(Order::STATUS_PROCESSING, Order::STATUS_DELIVERED));
    }

    /**
     * Valida la progresión secuencial del pipeline logístico: shipped -> delivered -> completed.
     */
    public function test_logistics_pipeline_progression(): void
    {
        // shipped -> delivered
        $this->assertTrue(Order::canTransition(Order::STATUS_SHIPPED, Order::STATUS_DELIVERED));
        $this->assertFalse(Order::canTransition(Order::STATUS_SHIPPED, Order::STATUS_COMPLETED));
        $this->assertFalse(Order::canTransition(Order::STATUS_SHIPPED, Order::STATUS_PROCESSING));

        // delivered -> completed o refunded
        $this->assertTrue(Order::canTransition(Order::STATUS_DELIVERED, Order::STATUS_COMPLETED));
        $this->assertTrue(Order::canTransition(Order::STATUS_DELIVERED, Order::STATUS_REFUNDED));
        $this->assertFalse(Order::canTransition(Order::STATUS_DELIVERED, Order::STATUS_SHIPPED));

        // completed -> refunded
        $this->assertTrue(Order::canTransition(Order::STATUS_COMPLETED, Order::STATUS_REFUNDED));
        $this->assertFalse(Order::canTransition(Order::STATUS_COMPLETED, Order::STATUS_SHIPPED));
        $this->assertFalse(Order::canTransition(Order::STATUS_COMPLETED, Order::STATUS_DELIVERED));
    }

    /**
     * Los estados terminales 'cancelled' y 'refunded' no permiten transiciones posteriores.
     */
    public function test_terminal_states_have_no_forward_transitions(): void
    {
        $this->assertEmpty(Order::getAllowedTransitions(Order::STATUS_CANCELLED));
        $this->assertEmpty(Order::getAllowedTransitions(Order::STATUS_REFUNDED));

        $allStatuses = array_keys(Order::STATUSES);

        foreach ($allStatuses as $targetStatus) {
            $this->assertFalse(
                Order::canTransition(Order::STATUS_CANCELLED, $targetStatus),
                "El estado 'cancelled' no debe transicionar a '{$targetStatus}'"
            );
            $this->assertFalse(
                Order::canTransition(Order::STATUS_REFUNDED, $targetStatus),
                "El estado 'refunded' no debe transicionar a '{$targetStatus}'"
            );
        }
    }

    /**
     * Cada estado de STATUSES cuenta con una asignación cromática coherente en STATUS_COLORS.
     */
    public function test_status_color_mapping_integrity(): void
    {
        $expectedColors = [
            Order::STATUS_PENDING    => 'warning',
            Order::STATUS_PROCESSING => 'info',
            Order::STATUS_SHIPPED    => 'primary',
            Order::STATUS_DELIVERED  => 'success',
            Order::STATUS_COMPLETED  => 'success',
            Order::STATUS_CANCELLED  => 'danger',
            Order::STATUS_REFUNDED   => 'secondary',
        ];

        foreach (Order::STATUSES as $statusKey => $statusLabel) {
            $this->assertArrayHasKey($statusKey, Order::STATUS_COLORS);
            $this->assertEquals($expectedColors[$statusKey], Order::getStatusColor($statusKey));
        }

        // Estado no contemplado debe devolver fallback 'secondary'
        $this->assertEquals('secondary', Order::getStatusColor('non_existent_status'));
    }

    /**
     * Verificación de robustez ante estados desconocidos o nulos.
     */
    public function test_unknown_status_transitions_and_validation(): void
    {
        $this->assertEmpty(Order::getAllowedTransitions('unknown_status'));
        $this->assertFalse(Order::canTransition('unknown_status', Order::STATUS_PENDING));
        $this->assertFalse(Order::canTransition(Order::STATUS_PENDING, 'invalid_destination'));
    }
}
