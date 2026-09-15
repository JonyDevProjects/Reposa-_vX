<?php

namespace App\Services\Shipping;

use App\Models\Order;
use App\Models\Shipment;

interface ShippingServiceInterface
{
    /**
     * Calcula las opciones de tarifa y tiempos de entrega según el importe del carrito y destino.
     *
     * @return array<int, array<string, mixed>>
     */
    public function calculateRates(float $cartTotal, ?string $postalCode = null, float $weightKg = 1.5): array;

    /**
     * Da de alta una expedición/envío formal vinculado a un pedido en el transportista.
     *
     * @param  array<string, mixed>  $recipientData
     */
    public function createShipment(Order $order, array $recipientData, string $serviceType = 'standard_48h'): Shipment;

    /**
     * Consulta el estado de trazabilidad y eventos de seguimiento mediante el número de tracking.
     *
     * @return array<string, mixed>|null
     */
    public function getTracking(string $trackingNumber): ?array;

    /**
     * Simula la transición y avance de hitos logísticos del paquete.
     */
    public function advanceTrackingStatus(Shipment $shipment, ?string $targetStatus = null): Shipment;

    /**
     * Genera la estructura de datos y el albarán/etiqueta de transporte.
     *
     * @return array<string, mixed>
     */
    public function generateLabel(Shipment $shipment): array;
}
