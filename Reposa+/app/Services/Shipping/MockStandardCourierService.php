<?php

namespace App\Services\Shipping;

use App\Models\Order;
use App\Models\Shipment;
use Carbon\Carbon;
use Illuminate\Support\Str;

class MockStandardCourierService implements ShippingServiceInterface
{
    const CARRIER_NAME = 'Correos Express';
    const FREE_SHIPPING_THRESHOLD = 50.0;

    const SENDER_INFO = [
        'company' => 'Reposa+ Sleep Wellness S.L.',
        'facility' => 'Hub Central Logístico Coslada',
        'street' => 'Av. de la Cañada 42, Nave 7',
        'city' => 'Coslada',
        'province' => 'Madrid',
        'zip_code' => '28823',
        'country' => 'ES',
        'contact_phone' => '+34 910 000 737',
    ];

    /**
     * Calcula las tarifas de envío disponibles para el carrito actual.
     */
    public function calculateRates(float $cartTotal, ?string $postalCode = null, float $weightKg = 1.5): array
    {
        $isFreeStandard = $cartTotal >= self::FREE_SHIPPING_THRESHOLD;

        return [
            [
                'id' => 'standard_48h',
                'carrier' => self::CARRIER_NAME,
                'name' => 'Reposa+ Estándar (48-72h)',
                'cost' => $isFreeStandard ? 0.00 : 4.95,
                'is_free' => $isFreeStandard,
                'estimated_days' => '2-3 días laborables',
                'description' => 'Entrega a domicilio con seguimiento continuo por SMS y correo electrónico.',
            ],
            [
                'id' => 'express_24h',
                'carrier' => self::CARRIER_NAME . ' Paq 24',
                'name' => 'Reposa+ Express (24h)',
                'cost' => 7.95,
                'is_free' => false,
                'estimated_days' => '1 día laborable',
                'description' => 'Entrega prioritaria antes de las 19:00h del siguiente día laborable.',
            ],
            [
                'id' => 'pickup_point',
                'carrier' => 'Red CityPaq / Correos',
                'name' => 'Punto de Recogida CityPaq (48h)',
                'cost' => 3.50,
                'is_free' => false,
                'estimated_days' => '2 días laborables',
                'description' => 'Recogida flexible en taquillas automáticas 24/7 u oficinas de Correos.',
            ],
        ];
    }

    /**
     * Da de alta una expedición simulada y genera su tracking y etiqueta técnica.
     */
    public function createShipment(Order $order, array $recipientData, string $serviceType = 'standard_48h'): Shipment
    {
        $trackingNumber = $this->generateTrackingNumber();
        $serviceName = match ($serviceType) {
            'express_24h' => 'Reposa+ Express (24h)',
            'pickup_point' => 'Punto de Recogida CityPaq (48h)',
            default => 'Reposa+ Estándar (48-72h)',
        };

        $shippingCost = match ($serviceType) {
            'express_24h' => 7.95,
            'pickup_point' => 3.50,
            default => ($order->total_amount >= self::FREE_SHIPPING_THRESHOLD ? 0.00 : 4.95),
        };

        $deliveryDays = match ($serviceType) {
            'express_24h' => 1,
            'pickup_point' => 2,
            default => 3,
        };

        $estimatedDelivery = $this->calculateBusinessDays(Carbon::now(), $deliveryDays);

        $initialEvent = [
            'timestamp' => Carbon::now()->toIso8601String(),
            'status' => Shipment::STATUS_PRE_REGISTERED,
            'status_label' => Shipment::STATUS_LABELS[Shipment::STATUS_PRE_REGISTERED],
            'description' => 'Datos de expedición generados telemáticamente. Paquete embalado en almacén central y etiquetado.',
            'location' => self::SENDER_INFO['facility'] . ' (' . self::SENDER_INFO['city'] . ')',
        ];

        $shipment = new Shipment([
            'order_id' => $order->id,
            'tracking_number' => $trackingNumber,
            'carrier' => self::CARRIER_NAME,
            'service_type' => $serviceType,
            'service_name' => $serviceName,
            'status' => Shipment::STATUS_PRE_REGISTERED,
            'shipping_cost' => $shippingCost,
            'recipient_name' => $recipientData['name'] ?? 'Cliente Reposa+',
            'recipient_email' => $recipientData['email'] ?? null,
            'recipient_phone' => $recipientData['phone'] ?? null,
            'street' => $recipientData['street'] ?? '',
            'city' => $recipientData['city'] ?? '',
            'zip_code' => $recipientData['zip_code'] ?? '',
            'province' => $recipientData['province'] ?? '',
            'country' => $recipientData['country'] ?? 'ES',
            'estimated_delivery_date' => $estimatedDelivery->toDateString(),
            'tracking_history' => [$initialEvent],
        ]);

        $shipment->label_data = $this->buildLabelData($shipment, $order);
        $shipment->save();

        return $shipment;
    }

    /**
     * Consulta el estado de trazabilidad y el historial cronológico de un envío.
     */
    public function getTracking(string $trackingNumber): ?array
    {
        $shipment = Shipment::where('tracking_number', $trackingNumber)->first();

        if (! $shipment) {
            return null;
        }

        return [
            'tracking_number' => $shipment->tracking_number,
            'carrier' => $shipment->carrier,
            'service_name' => $shipment->service_name,
            'status' => $shipment->status,
            'status_label' => $shipment->status_label,
            'status_color' => $shipment->status_color,
            'estimated_delivery' => $shipment->estimated_delivery_date?->format('d/m/Y'),
            'shipped_at' => $shipment->shipped_at?->format('d/m/Y H:i'),
            'delivered_at' => $shipment->delivered_at?->format('d/m/Y H:i'),
            'recipient' => [
                'name' => $shipment->recipient_name,
                'city' => $shipment->city,
                'zip_code' => $shipment->zip_code,
                'province' => $shipment->province,
            ],
            'events' => $shipment->tracking_history ?? [],
        ];
    }

    /**
     * Avanza el estado del paquete en la cadena logística simulada.
     */
    public function advanceTrackingStatus(Shipment $shipment, ?string $targetStatus = null): Shipment
    {
        $current = $shipment->status;
        $next = $targetStatus ?: match ($current) {
            Shipment::STATUS_PRE_REGISTERED => Shipment::STATUS_IN_TRANSIT,
            Shipment::STATUS_IN_TRANSIT => Shipment::STATUS_AT_HUB,
            Shipment::STATUS_AT_HUB => Shipment::STATUS_OUT_FOR_DELIVERY,
            Shipment::STATUS_OUT_FOR_DELIVERY => Shipment::STATUS_DELIVERED,
            default => $current,
        };

        if ($current === $next) {
            return $shipment;
        }

        $now = Carbon::now();
        $history = $shipment->tracking_history ?? [];

        $newEvent = match ($next) {
            Shipment::STATUS_IN_TRANSIT => [
                'timestamp' => $now->toIso8601String(),
                'status' => Shipment::STATUS_IN_TRANSIT,
                'status_label' => Shipment::STATUS_LABELS[Shipment::STATUS_IN_TRANSIT],
                'description' => 'Mercancía recogida por el transportista en Hub Central Coslada. En tránsito hacia centro de clasificación.',
                'location' => 'Madrid Hub Central',
            ],
            Shipment::STATUS_AT_HUB => [
                'timestamp' => $now->toIso8601String(),
                'status' => Shipment::STATUS_AT_HUB,
                'status_label' => Shipment::STATUS_LABELS[Shipment::STATUS_AT_HUB],
                'description' => "Envío recibido en la plataforma logística de destino ({$shipment->city}, {$shipment->province}). Clasificación completada.",
                'location' => "{$shipment->city} Delegación",
            ],
            Shipment::STATUS_OUT_FOR_DELIVERY => [
                'timestamp' => $now->toIso8601String(),
                'status' => Shipment::STATUS_OUT_FOR_DELIVERY,
                'status_label' => Shipment::STATUS_LABELS[Shipment::STATUS_OUT_FOR_DELIVERY],
                'description' => 'El paquete se encuentra en reparto con el mensajero. Entrega estimada en la franja habitual.',
                'location' => "{$shipment->city} Última Milla",
            ],
            Shipment::STATUS_DELIVERED => [
                'timestamp' => $now->toIso8601String(),
                'status' => Shipment::STATUS_DELIVERED,
                'status_label' => Shipment::STATUS_LABELS[Shipment::STATUS_DELIVERED],
                'description' => 'Envío entregado satisfactoriamente en la dirección del destinatario. Firmado digitalmente.',
                'location' => "{$shipment->city} Domicilio Destinatario",
            ],
            Shipment::STATUS_INCIDENT => [
                'timestamp' => $now->toIso8601String(),
                'status' => Shipment::STATUS_INCIDENT,
                'status_label' => Shipment::STATUS_LABELS[Shipment::STATUS_INCIDENT],
                'description' => 'Intento de entrega sin éxito: Destinatario ausente en domicilio. Se programa reintento automático.',
                'location' => "{$shipment->city}",
            ],
            default => [
                'timestamp' => $now->toIso8601String(),
                'status' => $next,
                'status_label' => ucfirst($next),
                'description' => 'Actualización de trazabilidad registrada.',
                'location' => 'Centro de Control Logístico',
            ],
        };

        $history[] = $newEvent;
        $shipment->tracking_history = $history;
        $shipment->status = $next;

        if ($next === Shipment::STATUS_IN_TRANSIT && ! $shipment->shipped_at) {
            $shipment->shipped_at = $now;
            // Sincronizar estado del pedido si corresponde
            if (in_array($shipment->order->status, [Order::STATUS_PENDING, Order::STATUS_PROCESSING])) {
                $shipment->order->update(['status' => Order::STATUS_SHIPPED]);
            }
        }

        if ($next === Shipment::STATUS_DELIVERED && ! $shipment->delivered_at) {
            $shipment->delivered_at = $now;
            if ($shipment->order->status === Order::STATUS_SHIPPED) {
                $shipment->order->update(['status' => Order::STATUS_DELIVERED]);
            }
        }

        $shipment->save();

        return $shipment;
    }

    /**
     * Genera la etiqueta técnica de paquetería estándar (formato A6 / 10x15cm).
     */
    public function generateLabel(Shipment $shipment): array
    {
        return $shipment->label_data ?? $this->buildLabelData($shipment, $shipment->order);
    }

    /**
     * Genera un número de seguimiento realista compatible con estándares de paquetería en España.
     * Ejemplo: RPX2026849201ES
     */
    protected function generateTrackingNumber(): string
    {
        $prefix = 'RPX' . date('Y');
        $random = str_pad((string) mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        $suffix = 'ES';

        return $prefix . $random . $suffix;
    }

    /**
     * Construye el payload de datos para la etiqueta de transporte térmico.
     */
    protected function buildLabelData(Shipment $shipment, Order $order): array
    {
        $routingCode = strtoupper(substr($shipment->province ?: $shipment->city, 0, 3)) . '-' . ($shipment->zip_code ?: '28001') . '-Z01';

        return [
            'carrier_logo' => 'Correos Express / Reposa+ Sleep Logistics',
            'tracking_number' => $shipment->tracking_number,
            'barcode_128' => '*' . $shipment->tracking_number . '*',
            'routing_code' => $routingCode,
            'service_type' => $shipment->service_name,
            'order_reference' => 'PED-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
            'sender' => self::SENDER_INFO,
            'recipient' => [
                'name' => $shipment->recipient_name,
                'phone' => $shipment->recipient_phone ?: 'No especificado',
                'street' => $shipment->street,
                'city' => $shipment->city,
                'province' => $shipment->province,
                'zip_code' => $shipment->zip_code,
                'country' => $shipment->country,
            ],
            'package' => [
                'type' => 'Bulto 1/1 - Almohada Ergonómica Especializada',
                'declared_weight_kg' => 1.80,
                'volumetric_weight_kg' => 3.60,
                'dimensions_cm' => '60x40x15',
            ],
            'instructions' => 'Tratar con cuidado. Producto textil sellado al vacío de descanso ergonómico. No doblar ni perforar.',
            'created_at' => Carbon::now()->format('d/m/Y H:i:s'),
        ];
    }

    /**
     * Calcula una fecha sumando únicamente días hábiles (lunes a viernes).
     */
    protected function calculateBusinessDays(Carbon $startDate, int $days): Carbon
    {
        $date = $startDate->copy();
        while ($days > 0) {
            $date->addDay();
            if (! $date->isWeekend()) {
                $days--;
            }
        }
        return $date;
    }
}
