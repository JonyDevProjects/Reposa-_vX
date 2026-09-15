<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Services\Cart\CartCalculator;
use PHPUnit\Framework\TestCase;

/**
 * Suite de pruebas unitarias puras para el motor de cálculo financiero del carrito
 * y verificación de todos los casos límite (edge cases) de cantidades, stock y fiscalidad.
 * Ejecución 100% en memoria sin persistencia relacional.
 */
class CartCalculatorUnitTest extends TestCase
{
    private CartCalculator $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new CartCalculator;
    }

    /**
     * Carrito vacío: debe calcular cero en todos los valores sin excepciones por división por cero.
     */
    public function test_empty_cart_calculations(): void
    {
        $totals = $this->calculator->calculateTotals([]);

        $this->assertSame(0.0, $totals['total']);
        $this->assertSame(0, $totals['items_count']);
        $this->assertSame(0.0, $totals['subtotal_net']);
        $this->assertSame(0.0, $totals['tax_vat']);
        $this->assertSame(50.0, $totals['free_shipping_threshold']);
        $this->assertFalse($totals['is_free_shipping']);
        $this->assertSame(50.0, $totals['remaining_for_free_shipping']);
        $this->assertSame(0, $totals['shipping_progress']);
    }

    /**
     * Cálculo de subtotal de línea y casos límite de cantidades negativas, nulas y precios.
     */
    public function test_line_subtotal_calculation_and_edge_cases(): void
    {
        // Caso estándar
        $this->assertSame(91.98, $this->calculator->calculateLineSubtotal(45.99, 2));

        // Caso límite: cantidad 0
        $this->assertSame(0.0, $this->calculator->calculateLineSubtotal(45.99, 0));

        // Caso límite: cantidad negativa (debe asegurarse en 0)
        $this->assertSame(0.0, $this->calculator->calculateLineSubtotal(45.99, -3));

        // Caso límite: precio 0 (artículo promocional o gratuito)
        $this->assertSame(0.0, $this->calculator->calculateLineSubtotal(0.0, 5));

        // Caso límite: precio negativo
        $this->assertSame(0.0, $this->calculator->calculateLineSubtotal(-10.0, 2));

        // Caso límite: redondeo de medios céntimos
        $this->assertSame(39.99, $this->calculator->calculateLineSubtotal(13.33, 3));
    }

    /**
     * Invariante contable estricto: la suma de base imponible neta + cuota de IVA (21%)
     * DEBE coincidir exactamente con el total general al céntimo en cualquier combinación.
     */
    public function test_accounting_invariant_net_plus_vat_equals_total(): void
    {
        $testCases = [
            [['price' => 45.99, 'quantity' => 1]],
            [['price' => 45.99, 'quantity' => 2]],
            [['price' => 35.50, 'quantity' => 1], ['price' => 59.99, 'quantity' => 1]],
            [['price' => 19.95, 'quantity' => 3], ['price' => 75.00, 'quantity' => 1]],
            [['price' => 0.99, 'quantity' => 7], ['price' => 12.49, 'quantity' => 2]],
        ];

        foreach ($testCases as $index => $items) {
            $totals = $this->calculator->calculateTotals($items);

            $sumNetAndVat = round($totals['subtotal_net'] + $totals['tax_vat'], 2);
            $this->assertEquals(
                $totals['total'],
                $sumNetAndVat,
                "Fallo de invariante contable en el caso {$index}: {$totals['subtotal_net']} + {$totals['tax_vat']} != {$totals['total']}"
            );
        }
    }

    /**
     * Casos límite del umbral de envío gratuito (50.00 €):
     * Verificación exhaustiva alrededor del límite (49.98, 49.99, 50.00, 50.01).
     */
    public function test_free_shipping_threshold_boundary_cases(): void
    {
        // 1. Caso límite: 0.00 €
        $atZero = $this->calculator->calculateTotals([['price' => 0.0, 'quantity' => 1]]);
        $this->assertFalse($atZero['is_free_shipping']);
        $this->assertSame(50.0, $atZero['remaining_for_free_shipping']);
        $this->assertSame(0, $atZero['shipping_progress']);

        // 2. Mitad del umbral: 25.00 €
        $atHalf = $this->calculator->calculateTotals([['price' => 25.0, 'quantity' => 1]]);
        $this->assertFalse($atHalf['is_free_shipping']);
        $this->assertSame(25.0, $atHalf['remaining_for_free_shipping']);
        $this->assertSame(50, $atHalf['shipping_progress']);

        // 3. Caso crítico: 49.99 € (un céntimo por debajo)
        // El porcentaje de progreso NUNCA debe redondearse a 100% para no engañar al usuario.
        $at49_99 = $this->calculator->calculateTotals([['price' => 49.99, 'quantity' => 1]]);
        $this->assertFalse($at49_99['is_free_shipping']);
        $this->assertSame(0.01, $at49_99['remaining_for_free_shipping']);
        $this->assertSame(99, $at49_99['shipping_progress']);

        // 4. Límite exacto: 50.00 €
        $at50 = $this->calculator->calculateTotals([['price' => 50.0, 'quantity' => 1]]);
        $this->assertTrue($at50['is_free_shipping']);
        $this->assertSame(0.0, $at50['remaining_for_free_shipping']);
        $this->assertSame(100, $at50['shipping_progress']);

        // 5. Un céntimo por encima: 50.01 €
        $at50_01 = $this->calculator->calculateTotals([['price' => 50.01, 'quantity' => 1]]);
        $this->assertTrue($at50_01['is_free_shipping']);
        $this->assertSame(0.0, $at50_01['remaining_for_free_shipping']);
        $this->assertSame(100, $at50_01['shipping_progress']);

        // 6. Muy por encima: 200.00 € (el progreso no debe superar 100%)
        $at200 = $this->calculator->calculateTotals([['price' => 100.0, 'quantity' => 2]]);
        $this->assertTrue($at200['is_free_shipping']);
        $this->assertSame(0.0, $at200['remaining_for_free_shipping']);
        $this->assertSame(100, $at200['shipping_progress']);
    }

    /**
     * Casos límite del ajuste defensivo de cantidad (clampQuantity):
     * Negativos, ceros, límites de stock y límites máximos del sistema.
     */
    public function test_clamp_quantity_edge_cases(): void
    {
        // 1. Cantidad normal dentro de stock
        $this->assertSame(3, $this->calculator->clampQuantity(3, 10));

        // 2. Cantidad menor al mínimo (0 o negativa) -> ajusta a min (1)
        $this->assertSame(1, $this->calculator->clampQuantity(0, 10));
        $this->assertSame(1, $this->calculator->clampQuantity(-5, 10));

        // 3. Cantidad que supera el stock disponible -> ajusta al stock disponible
        $this->assertSame(10, $this->calculator->clampQuantity(15, 10));

        // 4. Stock es exactamente 0 (sin existencias) -> retorna 0
        $this->assertSame(0, $this->calculator->clampQuantity(3, 0));
        $this->assertSame(0, $this->calculator->clampQuantity(1, -2));

        // 5. Cantidad igual al stock disponible -> retorna el valor intacto
        $this->assertSame(10, $this->calculator->clampQuantity(10, 10));

        // 6. Superación de límite máximo configurado (ej: 999)
        $this->assertSame(999, $this->calculator->clampQuantity(1500, 2000, 1, 999));
    }

    /**
     * Validación de entradas de usuario para cantidad:
     * Detección precisa de tipos no válidos, cero, stock rebasado y existencias nulas.
     */
    public function test_validate_quantity_edge_cases(): void
    {
        // 1. Entrada válida
        $valid = $this->calculator->validateQuantity(4, 10);
        $this->assertTrue($valid['valid']);
        $this->assertSame(4, $valid['quantity']);
        $this->assertNull($valid['error_code']);

        // 2. Entrada no numérica o decimal flotante
        $nonNumeric = $this->calculator->validateQuantity('abc', 10);
        $this->assertFalse($nonNumeric['valid']);
        $this->assertSame('invalid_integer', $nonNumeric['error_code']);

        $floatVal = $this->calculator->validateQuantity(2.5, 10);
        $this->assertFalse($floatVal['valid']);
        $this->assertSame('invalid_integer', $floatVal['error_code']);

        // 3. Cantidad inferior al mínimo (0 o negativo)
        $zero = $this->calculator->validateQuantity(0, 10);
        $this->assertFalse($zero['valid']);
        $this->assertSame('min_quantity', $zero['error_code']);

        $negative = $this->calculator->validateQuantity(-2, 10);
        $this->assertFalse($negative['valid']);
        $this->assertSame('min_quantity', $negative['error_code']);

        // 4. Stock agotado (stock = 0)
        $outOfStock = $this->calculator->validateQuantity(1, 0);
        $this->assertFalse($outOfStock['valid']);
        $this->assertSame('out_of_stock', $outOfStock['error_code']);

        // 5. Cantidad superior al stock disponible
        $exceeds = $this->calculator->validateQuantity(15, 8);
        $this->assertFalse($exceeds['valid']);
        $this->assertSame('exceeds_stock', $exceeds['error_code']);
        $this->assertSame(8, $exceeds['max_available']);
    }

    /**
     * Soporte tanto para arrays como para objetos de Eloquent en calculateTotals.
     */
    public function test_calculate_totals_supports_both_arrays_and_objects(): void
    {
        $objectItems = collect([
            (object) [
                'quantity' => 2,
                'product' => (object) ['price' => 45.00],
            ],
            (object) [
                'quantity' => 1,
                'product' => (object) ['price' => 10.00],
            ],
        ]);

        $totals = $this->calculator->calculateTotals($objectItems);

        $this->assertSame(100.00, $totals['total']);
        $this->assertSame(3, $totals['items_count']);
        $this->assertTrue($totals['is_free_shipping']);
        $this->assertSame(100, $totals['shipping_progress']);
        $this->assertSame('100,00 €', $totals['formatted']['total']);
    }
}
