<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\User;
use PHPUnit\Framework\TestCase;

/**
 * Suite de pruebas unitarias puras para la lógica de dominio en memoria del modelo Order.
 * Verifica la resolución de identidad (isGuest), prioridad de snapshots y fallbacks defensivos sin base de datos.
 */
class OrderDomainLogicUnitTest extends TestCase
{
    /**
     * isGuest() debe devolver true cuando user_id es null (compra como invitado).
     */
    public function test_is_guest_returns_true_when_user_id_is_null(): void
    {
        $order = new Order(['user_id' => null]);
        $this->assertTrue($order->isGuest());

        $emptyOrder = new Order;
        $this->assertTrue($emptyOrder->isGuest());
    }

    /**
     * isGuest() debe devolver false cuando user_id contiene un identificador válido.
     */
    public function test_is_guest_returns_false_when_user_id_is_present(): void
    {
        $order = new Order(['user_id' => 42]);
        $this->assertFalse($order->isGuest());
    }

    /**
     * El accessor customer_name debe priorizar el snapshot inmutable shipping_name sobre el usuario relacionado.
     */
    public function test_customer_name_prioritizes_shipping_name_snapshot(): void
    {
        $user = new User([
            'name' => 'Usuario Registrado Original',
            'email' => 'registrado@reposaplus.es',
        ]);

        $order = new Order([
            'user_id' => 10,
            'shipping_name' => 'Destinatario Regalo Snapshot',
        ]);
        $order->setRelation('user', $user);

        $this->assertEquals('Destinatario Regalo Snapshot', $order->customer_name);
    }

    /**
     * El accessor customer_email debe priorizar el snapshot inmutable shipping_email sobre el correo del usuario.
     */
    public function test_customer_email_prioritizes_shipping_email_snapshot(): void
    {
        $user = new User([
            'name' => 'Usuario Registrado',
            'email' => 'usuario@reposaplus.es',
        ]);

        $order = new Order([
            'user_id' => 15,
            'shipping_email' => 'facturacion.alternativa@empresa.com',
        ]);
        $order->setRelation('user', $user);

        $this->assertEquals('facturacion.alternativa@empresa.com', $order->customer_email);
    }

    /**
     * Cuando no existe snapshot shipping_name, customer_name recurre al nombre del usuario autenticado.
     */
    public function test_customer_name_resolves_to_user_name_when_snapshot_is_absent(): void
    {
        $user = new User([
            'name' => 'Laura García',
            'email' => 'laura@example.com',
        ]);

        $order = new Order([
            'user_id' => 5,
            'shipping_name' => null,
        ]);
        $order->setRelation('user', $user);

        $this->assertEquals('Laura García', $order->customer_name);
    }

    /**
     * Cuando no existe snapshot shipping_email, customer_email recurre al correo del usuario autenticado.
     */
    public function test_customer_email_resolves_to_user_email_when_snapshot_is_absent(): void
    {
        $user = new User([
            'name' => 'Laura García',
            'email' => 'laura@example.com',
        ]);

        $order = new Order([
            'user_id' => 5,
            'shipping_email' => null,
        ]);
        $order->setRelation('user', $user);

        $this->assertEquals('laura@example.com', $order->customer_email);
    }

    /**
     * Si no existe ni snapshot de envío ni usuario relacionado, deben operar los fallbacks por defecto.
     */
    public function test_customer_name_and_email_fallback_for_empty_data(): void
    {
        $order = new Order;

        $this->assertEquals('Cliente Reposa+', $order->customer_name);
        $this->assertEquals('', $order->customer_email);
    }
}
