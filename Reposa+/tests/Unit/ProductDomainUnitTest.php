<?php

namespace Tests\Unit;

use App\Models\Product;
use PHPUnit\Framework\TestCase;

/**
 * Suite de pruebas unitarias puras para las reglas de negocio y cálculo del modelo Product.
 * Ejecución 100% en memoria sin persistencia relacional.
 */
class ProductDomainUnitTest extends TestCase
{
    /**
     * isInStock() evalúa con precisión existencias positivas frente a agotadas o nulas.
     */
    public function test_in_stock_determination(): void
    {
        $productInStock = new Product(['stock' => 12]);
        $this->assertTrue($productInStock->isInStock());

        $productBorderline = new Product(['stock' => 1]);
        $this->assertTrue($productBorderline->isInStock());

        $productOutOfStock = new Product(['stock' => 0]);
        $this->assertFalse($productOutOfStock->isInStock());

        $productNegativeStock = new Product(['stock' => -5]);
        $this->assertFalse($productNegativeStock->isInStock());

        $productNullStock = new Product();
        $this->assertFalse($productNullStock->isInStock());
    }

    /**
     * hasStock() valida umbrales de cantidad solicitada frente al inventario disponible.
     */
    public function test_has_stock_quantity_evaluation(): void
    {
        $product = new Product(['stock' => 5]);

        // Cantidad inferior al stock disponible
        $this->assertTrue($product->hasStock(1));
        $this->assertTrue($product->hasStock(4));

        // Cantidad exacta límite
        $this->assertTrue($product->hasStock(5));

        // Cantidad que excede el stock
        $this->assertFalse($product->hasStock(6));
        $this->assertFalse($product->hasStock(10));

        // Cantidades inválidas o no positivas
        $this->assertFalse($product->hasStock(0));
        $this->assertFalse($product->hasStock(-1));
    }

    /**
     * calculateSubtotal() preserva la precisión aritmética monetaria de 2 decimales sin desbordamiento de coma flotante.
     */
    public function test_price_precision_and_calculations(): void
    {
        // 19.99 * 3 suele derivar en 59.970000000000006 en coma flotante no redondeada
        $pillow = new Product(['price' => 19.99]);
        $this->assertSame(59.97, $pillow->calculateSubtotal(3));

        $premiumPillow = new Product(['price' => 29.95]);
        $this->assertSame(59.90, $premiumPillow->calculateSubtotal(2));

        $standardPillow = new Product(['price' => 33.33]);
        $this->assertSame(99.99, $standardPillow->calculateSubtotal(3));

        // Cantidad 0 o negativa debe computar 0.00
        $this->assertSame(0.0, $pillow->calculateSubtotal(0));
        $this->assertSame(0.0, $pillow->calculateSubtotal(-2));
    }
}
