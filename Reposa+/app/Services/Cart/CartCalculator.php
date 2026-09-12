<?php

declare(strict_types=1);

namespace App\Services\Cart;

/**
 * Servicio puro de cálculo y reglas de negocio para el carrito de compras.
 * Gestiona subtotales, totales, base imponible, desglose de IVA (21%),
 * umbrales de envío gratuito (50.00 €) y control estricto de casos límite de cantidad y stock.
 */
class CartCalculator
{
    public const FREE_SHIPPING_THRESHOLD = 50.0;

    public const VAT_RATE = 0.21;

    public const DEFAULT_MIN_QUANTITY = 1;

    public const DEFAULT_MAX_QUANTITY = 999;

    /**
     * Calcula el subtotal de una línea de producto garantizando redondeo financiero a 2 decimales.
     * Casos límite: cantidades <= 0 retornan 0.00 €, precios negativos retornan 0.00 €.
     */
    public function calculateLineSubtotal(float $unitPrice, int $quantity): float
    {
        $safePrice = max(0.0, $unitPrice);
        $safeQuantity = max(0, $quantity);

        return round($safePrice * $safeQuantity, 2);
    }

    /**
     * Calcula los totales consolidados de una colección o listado de elementos del carrito.
     * Garantiza el invariante contable: subtotal_net + tax_vat === total.
     *
     * @param  iterable  $items  Colección o array con cantidades y productos
     */
    public function calculateTotals(iterable $items): array
    {
        $total = 0.0;
        $itemsCount = 0;

        foreach ($items as $item) {
            $qty = (int) (is_array($item) ? ($item['quantity'] ?? 0) : ($item->quantity ?? 0));
            $price = (float) (
                is_array($item)
                    ? ($item['product']['price'] ?? $item['price'] ?? 0.0)
                    : ($item->product->price ?? $item->price ?? 0.0)
            );

            if ($qty > 0) {
                $itemsCount += $qty;
                $total += $this->calculateLineSubtotal($price, $qty);
            }
        }

        $total = round($total, 2);
        // Base imponible neta desglosando el 21% de IVA
        $subtotalNet = $total > 0 ? round($total / (1.0 + self::VAT_RATE), 2) : 0.0;
        // La cuota de IVA se calcula por diferencia para evitar descuadres de 1 céntimo
        $taxVat = round($total - $subtotalNet, 2);

        $isFreeShipping = $total >= self::FREE_SHIPPING_THRESHOLD;
        $remainingForFreeShipping = $isFreeShipping ? 0.0 : round(self::FREE_SHIPPING_THRESHOLD - $total, 2);

        // Progreso de envío: si el total es 0 es 0%. Si alcanza 50€ es 100%.
        // En el caso límite 49.99€, nunca debe redondear hacia arriba al 100% (se queda en 99%).
        if ($total <= 0.0) {
            $shippingProgress = 0;
        } elseif ($isFreeShipping) {
            $shippingProgress = 100;
        } else {
            $shippingProgress = (int) min(99, max(0, floor(($total / self::FREE_SHIPPING_THRESHOLD) * 100)));
        }

        return [
            'total' => $total,
            'items_count' => $itemsCount,
            'subtotal_net' => $subtotalNet,
            'tax_vat' => $taxVat,
            'free_shipping_threshold' => self::FREE_SHIPPING_THRESHOLD,
            'is_free_shipping' => $isFreeShipping,
            'remaining_for_free_shipping' => $remainingForFreeShipping,
            'shipping_progress' => $shippingProgress,
            'formatted' => [
                'total' => number_format($total, 2, ',', '.').' €',
                'subtotal_net' => number_format($subtotalNet, 2, ',', '.').' €',
                'tax_vat' => number_format($taxVat, 2, ',', '.').' €',
                'remaining_for_free_shipping' => number_format($remainingForFreeShipping, 2, ',', '.').' €',
            ],
        ];
    }

    /**
     * Ajusta (clamps) una cantidad solicitada a los límites seguros del sistema y del stock disponible.
     */
    public function clampQuantity(
        int $quantity,
        int $stock,
        int $min = self::DEFAULT_MIN_QUANTITY,
        int $max = self::DEFAULT_MAX_QUANTITY
    ): int {
        if ($stock <= 0) {
            return 0;
        }

        $effectiveMax = min($stock, $max);

        if ($quantity < $min) {
            return $min;
        }

        if ($quantity > $effectiveMax) {
            return $effectiveMax;
        }

        return $quantity;
    }

    /**
     * Valida una cantidad solicitada devolviendo el código de error correspondiente a los casos límite.
     */
    public function validateQuantity(
        mixed $quantity,
        int $stock,
        int $min = self::DEFAULT_MIN_QUANTITY,
        int $max = self::DEFAULT_MAX_QUANTITY
    ): array {
        if (! is_numeric($quantity) || (int) $quantity != $quantity) {
            return [
                'valid' => false,
                'quantity' => $min,
                'error_code' => 'invalid_integer',
                'message_key' => 'messages.cart.quantity_min_one',
            ];
        }

        $qty = (int) $quantity;

        if ($qty < $min) {
            return [
                'valid' => false,
                'quantity' => $min,
                'error_code' => 'min_quantity',
                'message_key' => 'messages.cart.quantity_min_one',
            ];
        }

        if ($stock <= 0) {
            return [
                'valid' => false,
                'quantity' => 0,
                'error_code' => 'out_of_stock',
                'message_key' => 'messages.cart.stock_unavailable',
            ];
        }

        $effectiveMax = min($stock, $max);

        if ($qty > $effectiveMax) {
            return [
                'valid' => false,
                'quantity' => $effectiveMax,
                'error_code' => 'exceeds_stock',
                'max_available' => $effectiveMax,
                'message_key' => 'messages.cart.only_left',
            ];
        }

        return [
            'valid' => true,
            'quantity' => $qty,
            'error_code' => null,
            'message_key' => null,
        ];
    }
}
