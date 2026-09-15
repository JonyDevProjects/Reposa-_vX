<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Refund;
use App\Models\Shipment;
use App\Models\User;
use App\Services\Shipping\ShippingServiceInterface;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderTestMatrixSeeder extends Seeder
{
    /**
     * Limpia y regenera una matriz de pedidos limpia y coherente para pruebas de ciclo de vida.
     */
    public function run(): void
    {
        // 1. Limpieza de tablas de pedidos existentes
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Refund::truncate();
        OrderItem::truncate();
        Shipment::truncate();
        Order::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $user = User::where('email', 'user@reposaplus.com')->first() ?? User::factory()->create([
            'name' => 'Jonathan Quishpe',
            'email' => 'jvtheproducerofficial@gmail.com',
            'role' => 'user',
        ]);

        $products = Product::all();
        if ($products->isEmpty()) {
            $this->call(DatabaseSeeder::class);
            $products = Product::all();
        }

        $p1 = $products[0];
        $p2 = $products[1] ?? $products[0];
        $p3 = $products[2] ?? $products[0];

        $shippingService = app(ShippingServiceInterface::class);

        // Matriz de 9 pedidos ejemplares
        $matrix = [
            // 1. Pendiente - Pago con Stripe autorizado
            [
                'customer_name' => 'Carlos Méndez',
                'customer_email' => 'carlos.mendez@example.com',
                'status' => Order::STATUS_PENDING,
                'stripe' => true,
                'paid' => true,
                'shipment_status' => Shipment::STATUS_PRE_REGISTERED,
                'days_ago' => 1,
            ],
            // 2. Procesando - Pago directo confirmado, en almacén
            [
                'customer_name' => 'Marta Gómez',
                'customer_email' => 'marta.gomez@example.com',
                'status' => Order::STATUS_PROCESSING,
                'stripe' => false,
                'paid' => true,
                'shipment_status' => Shipment::STATUS_PRE_REGISTERED,
                'days_ago' => 2,
            ],
            // 3. Enviado - En tránsito Coslada -> Destino
            [
                'customer_name' => 'David Serrano',
                'customer_email' => 'david.serrano@example.com',
                'status' => Order::STATUS_SHIPPED,
                'stripe' => true,
                'paid' => true,
                'shipment_status' => Shipment::STATUS_IN_TRANSIT,
                'days_ago' => 3,
            ],
            // 4. Enviado - En reparto última milla
            [
                'customer_name' => 'Lucía Fernández',
                'customer_email' => 'lucia.fernandez@example.com',
                'status' => Order::STATUS_SHIPPED,
                'stripe' => true,
                'paid' => true,
                'shipment_status' => Shipment::STATUS_OUT_FOR_DELIVERY,
                'days_ago' => 4,
            ],
            // 5. Entregado - Paquete entregado, listo para completar o reembolsar
            [
                'customer_name' => 'Alejandro Sanz',
                'customer_email' => 'alejandro.sanz@example.com',
                'status' => Order::STATUS_DELIVERED,
                'stripe' => true,
                'paid' => true,
                'shipment_status' => Shipment::STATUS_DELIVERED,
                'days_ago' => 5,
            ],
            // 6. Completado - Cliente registrado con Stripe (permite reembolso formal)
            [
                'customer_name' => 'Jonathan Quishpe',
                'customer_email' => 'jvtheproducerofficial@gmail.com',
                'status' => Order::STATUS_COMPLETED,
                'stripe' => true,
                'paid' => true,
                'shipment_status' => Shipment::STATUS_DELIVERED,
                'days_ago' => 6,
            ],
            // 7. Completado - Invitado compra directa (cerrado, sin pasarela Stripe)
            [
                'customer_name' => 'Laura Invitada E2E',
                'customer_email' => 'laura.invitada@reposatest.es',
                'status' => Order::STATUS_COMPLETED,
                'stripe' => false,
                'paid' => true,
                'guest' => true,
                'shipment_status' => Shipment::STATUS_DELIVERED,
                'days_ago' => 7,
            ],
            // 8. Cancelado - Pedido anulado en origen
            [
                'customer_name' => 'Sofía Navarro',
                'customer_email' => 'sofia.navarro@example.com',
                'status' => Order::STATUS_CANCELLED,
                'stripe' => false,
                'paid' => false,
                'shipment_status' => Shipment::STATUS_CANCELLED,
                'days_ago' => 8,
            ],
            // 9. Reembolsado - Devolución formal completada
            [
                'customer_name' => 'Pedro Almodóvar',
                'customer_email' => 'pedro.almodovar@example.com',
                'status' => Order::STATUS_REFUNDED,
                'stripe' => true,
                'paid' => true,
                'refunded' => true,
                'shipment_status' => Shipment::STATUS_DELIVERED,
                'days_ago' => 9,
            ],
        ];

        foreach ($matrix as $index => $item) {
            $orderDate = Carbon::now()->subDays($item['days_ago'])->setHour(10 + $index)->setMinute(15 + $index);
            $total = round($p1->price + ($index % 2 == 0 ? $p2->price : 0), 2);
            $isGuest = $item['guest'] ?? false;

            $order = Order::create([
                'user_id' => $isGuest ? null : $user->id,
                'guest_token' => $isGuest ? Str::random(40) : null,
                'shipping_name' => $item['customer_name'],
                'shipping_email' => $item['customer_email'],
                'shipping_phone' => '+34 6'.str_pad((string) (10000000 + $index * 111111), 8, '0'),
                'shipping_street' => 'Calle Gran Vía '.($index + 10).', 3º B',
                'shipping_city' => 'Madrid',
                'shipping_zip_code' => '28013',
                'shipping_province' => 'Madrid',
                'shipping_country' => 'ES',
                'shipping_service_type' => 'standard_48h',
                'shipping_cost' => 4.95,
                'total_amount' => $total,
                'status' => $item['status'],
                'stripe_session_id' => $item['stripe'] ? 'cs_test_matrix_'.Str::random(24) : null,
                'payment_intent_id' => ($item['stripe'] && $item['paid']) ? 'pi_3UE'.Str::random(21) : null,
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            // Crear líneas de pedido
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $p1->id,
                'quantity' => 1,
                'price_at_purchase' => $p1->price,
            ]);

            if ($index % 2 == 0) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $p2->id,
                    'quantity' => 1,
                    'price_at_purchase' => $p2->price,
                ]);
            }

            // Crear envío logístico correspondiente
            $shippedAt = in_array($item['shipment_status'], [Shipment::STATUS_IN_TRANSIT, Shipment::STATUS_AT_HUB, Shipment::STATUS_OUT_FOR_DELIVERY, Shipment::STATUS_DELIVERED])
                ? $orderDate->copy()->addHours(2)
                : null;

            $deliveredAt = ($item['shipment_status'] === Shipment::STATUS_DELIVERED)
                ? $orderDate->copy()->addDays(1)
                : null;

            $shipment = $shippingService->createShipment($order, [
                'name' => $item['customer_name'],
                'email' => $item['customer_email'],
                'phone' => '+34 6'.str_pad((string) (10000000 + $index * 111111), 8, '0'),
                'street' => 'Calle Gran Vía '.($index + 10).', 3º B',
                'city' => 'Madrid',
                'zip_code' => '28013',
                'province' => 'Madrid',
                'country' => 'ES',
            ], 'standard_48h');

            $shipment->update([
                'status' => $item['shipment_status'],
                'shipped_at' => $shippedAt,
                'delivered_at' => $deliveredAt,
            ]);

            // Si es pedido reembolsado, crear registro formal de reembolso
            if (! empty($item['refunded'])) {
                Refund::create([
                    'order_id' => $order->id,
                    'amount' => $order->total_amount,
                    'reason' => 'Devolución técnica certificada por administración.',
                    'stripe_refund_id' => 're_test_matrix_'.Str::random(20),
                    'status' => 'succeeded',
                    'created_at' => $orderDate->copy()->addDays(2),
                ]);
            }
        }
    }
}
