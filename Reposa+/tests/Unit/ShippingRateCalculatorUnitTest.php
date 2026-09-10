<?php

namespace Tests\Unit;

use App\Services\Shipping\MockStandardCourierService;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

/**
 * Suite de pruebas unitarias puras para el motor de tarifas y algoritmos de paquetería.
 * Ejecución 100% en memoria sin persistencia relacional.
 */
class ShippingRateCalculatorUnitTest extends TestCase
{
    private MockStandardCourierService $courier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->courier = new MockStandardCourierService;
    }

    /**
     * Carritos con importe total >= 50.00 € deben beneficiarse de gastos de envío estándar gratuitos (0.00 €).
     */
    public function test_free_shipping_threshold_at_50_euros(): void
    {
        // En el límite exacto de 50.00 €
        $ratesAtLimit = $this->courier->calculateRates(50.00);
        $standardRate = $this->extractRateById($ratesAtLimit, 'standard_48h');

        $this->assertNotNull($standardRate);
        $this->assertEquals(0.00, $standardRate['cost']);
        $this->assertTrue($standardRate['is_free']);

        // Con importe superior (ej: 89.90 €)
        $ratesAboveLimit = $this->courier->calculateRates(89.90);
        $standardRateAbove = $this->extractRateById($ratesAboveLimit, 'standard_48h');

        $this->assertNotNull($standardRateAbove);
        $this->assertEquals(0.00, $standardRateAbove['cost']);
        $this->assertTrue($standardRateAbove['is_free']);
    }

    /**
     * Carritos con importe total < 50.00 € deben computar tarifa estándar de exactamente 4.95 €.
     */
    public function test_standard_shipping_rate_below_threshold(): void
    {
        // Justo un céntimo por debajo (49.99 €)
        $ratesJustBelow = $this->courier->calculateRates(49.99);
        $standardRate = $this->extractRateById($ratesJustBelow, 'standard_48h');

        $this->assertNotNull($standardRate);
        $this->assertEquals(4.95, $standardRate['cost']);
        $this->assertFalse($standardRate['is_free']);

        // Carrito de bajo importe (15.00 €)
        $ratesLow = $this->courier->calculateRates(15.00);
        $standardRateLow = $this->extractRateById($ratesLow, 'standard_48h');

        $this->assertNotNull($standardRateLow);
        $this->assertEquals(4.95, $standardRateLow['cost']);
        $this->assertFalse($standardRateLow['is_free']);
    }

    /**
     * Las opciones express_24h (7.95 €) y pickup_point (3.50 €) tienen importes fijos independientes del total.
     */
    public function test_fixed_shipping_rates(): void
    {
        $testTotals = [10.00, 49.99, 50.00, 150.00];

        foreach ($testTotals as $total) {
            $rates = $this->courier->calculateRates($total);

            $express = $this->extractRateById($rates, 'express_24h');
            $this->assertNotNull($express);
            $this->assertEquals(7.95, $express['cost']);
            $this->assertFalse($express['is_free']);

            $pickup = $this->extractRateById($rates, 'pickup_point');
            $this->assertNotNull($pickup);
            $this->assertEquals(3.50, $pickup['cost']);
            $this->assertFalse($pickup['is_free']);
        }
    }

    /**
     * Todo número de seguimiento generado debe coincidir estrictamente con el patrón regex ^RPX\d{4}\d{6}ES$.
     */
    public function test_tracking_number_format_compliance(): void
    {
        $currentYear = date('Y');
        $expectedRegex = '/^RPX'.$currentYear.'\d{6}ES$/';

        for ($i = 0; $i < 25; $i++) {
            $tracking = $this->courier->generateTrackingNumber();

            $this->assertEquals(15, strlen($tracking));
            $this->assertMatchesRegularExpression($expectedRegex, $tracking);
            $this->assertStringStartsWith('RPX'.$currentYear, $tracking);
            $this->assertStringEndsWith('ES', $tracking);
        }
    }

    /**
     * El cálculo de entrega estimada debe excluir fines de semana (sábado y domingo).
     */
    public function test_business_day_delivery_calculation(): void
    {
        // Viernes 11 de septiembre de 2026
        $friday = Carbon::create(2026, 9, 11, 10, 0, 0);
        $this->assertTrue($friday->isFriday());

        // +1 día hábil desde viernes debe saltar sábado y domingo y caer en lunes 14
        $plusOneBusinessDay = $this->courier->calculateBusinessDays($friday, 1);
        $this->assertEquals('2026-09-14', $plusOneBusinessDay->toDateString());
        $this->assertTrue($plusOneBusinessDay->isMonday());

        // +2 días hábiles desde viernes debe caer en martes 15
        $plusTwoBusinessDays = $this->courier->calculateBusinessDays($friday, 2);
        $this->assertEquals('2026-09-15', $plusTwoBusinessDays->toDateString());
        $this->assertTrue($plusTwoBusinessDays->isTuesday());

        // +3 días hábiles desde viernes debe caer en miércoles 16
        $plusThreeBusinessDays = $this->courier->calculateBusinessDays($friday, 3);
        $this->assertEquals('2026-09-16', $plusThreeBusinessDays->toDateString());
        $this->assertTrue($plusThreeBusinessDays->isWednesday());

        // Lunes 14 de septiembre de 2026 + 5 días hábiles debe llegar al siguiente lunes 21
        $monday = Carbon::create(2026, 9, 14, 9, 0, 0);
        $plusFiveBusinessDays = $this->courier->calculateBusinessDays($monday, 5);
        $this->assertEquals('2026-09-21', $plusFiveBusinessDays->toDateString());
        $this->assertTrue($plusFiveBusinessDays->isMonday());
    }

    /**
     * Helper para extraer una tarifa por su identificador de servicio.
     */
    private function extractRateById(array $rates, string $rateId): ?array
    {
        foreach ($rates as $rate) {
            if (($rate['id'] ?? '') === $rateId) {
                return $rate;
            }
        }

        return null;
    }
}
