<?php

namespace App\Services\Shipping;

use App\Models\Order;
use App\Models\Shipment;

interface ShippingServiceInterface
{
    /**
     * Calcula las opciones de tarifa y tiempos de entrega según el importe del carrito y destino.
     *
     * @param float $cartTotal
     * @param string|null $postalCode
     * @param float $weightKg
     * @return array<int, array<string, mixed>>
     */
    public function calculateRates(float $cartTotal, ?string $postalCode = null, float $weightKg = 1.5): array;

    /**
     * Da de alta una expedición/envío formal vinculado a un pedido en el transportista.
     *
     * @param Order $order
     * @param array<string, mixed> $recipientData
     * @param string $serviceType
     * @return Shipment
     */
    public function createShipment(Order $order, array $recipientData, string $serviceType = 'standard_48h'): Shipment;

    /**
     * Consulta el estado de trazabilidad y eventos de seguimiento mediante el número de tracking.
     *
     * @param string $trackingNumber
     * @return array<string, mixed>|null
     */
    public function getTracking(string $trackingNumber): ?array;

    /**
     * Simula la transición y avance de hitos logísticos del paquete.
     *
     * @param Shipment $shipment
     * @param string|null $targetStatus
     * @return Shipment
     */
    public function advanceTrackingStatus(Shipment $shipment, ?string $targetStatus = null): Shipment;

    /**
     * Genera la estructura de datos y el albarán/etiqueta de transporte.
     *
     * @param Shipment $shipment
     * @return array<string, mixed>
     */
    public function generateLabel(Shipment $shipment): array;
}
