<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Suite de pruebas de integración para la reactividad en tiempo real del carrito
 * y verificación de respuestas JSON para casos límite y sincronización de estado.
 */
class CartRealtimeUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Un usuario autenticado puede actualizar la cantidad de un producto vía JSON
     * y recibir subtotales, totales, desglose de IVA y umbral de envío actualizados.
     */
    public function test_auth_user_can_update_cart_quantity_via_json(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'stock' => 10,
            'price' => 20.00,
        ]);

        $cartItem = CartItem::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($user)
            ->postJson("/cart/update/{$cartItem->id}", ['quantity' => 2]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'item' => [
                    'id' => (string) $cartItem->id,
                    'quantity' => 2,
                    'unit_price' => 20.00,
                    'subtotal' => 40.00,
                ],
                'totals' => [
                    'total' => 40.00,
                    'items_count' => 2,
                    'is_free_shipping' => false,
                    'remaining_for_free_shipping' => 10.00,
                    'shipping_progress' => 80,
                ],
                'cart_count' => 2,
            ]);

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 2,
        ]);
    }

    /**
     * Un invitado en sesión puede actualizar la cantidad vía JSON sin autenticación.
     */
    public function test_guest_can_update_cart_quantity_via_json(): void
    {
        $product = Product::factory()->create([
            'stock' => 10,
            'price' => 30.00,
        ]);

        $sessionData = [
            'cart' => [
                $product->id => ['quantity' => 1],
            ],
        ];

        $response = $this->withSession($sessionData)
            ->postJson("/cart/update/{$product->id}", ['quantity' => 2]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'item' => [
                    'quantity' => 2,
                    'subtotal' => 60.00,
                ],
                'totals' => [
                    'total' => 60.00,
                    'items_count' => 2,
                    'is_free_shipping' => true,
                    'remaining_for_free_shipping' => 0,
                    'shipping_progress' => 100,
                ],
                'cart_count' => 2,
            ]);

        $this->assertSame(2, session('cart')[$product->id]['quantity']);
    }

    /**
     * Caso límite: Solicitud de cantidad superior al stock disponible debe devolver HTTP 422 JSON
     * y especificar el stock máximo disponible.
     */
    public function test_update_quantity_exceeding_stock_returns_422_json(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Almohada Cervical Visco',
            'stock' => 3,
            'price' => 45.00,
        ]);

        $cartItem = CartItem::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($user)
            ->postJson("/cart/update/{$cartItem->id}", ['quantity' => 5]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'error_code' => 'exceeds_stock',
                'max_stock' => 3,
                'current_quantity' => 2,
            ]);

        // La base de datos no debe verse alterada
        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 2,
        ]);
    }

    /**
     * Caso límite: Cantidad 0 o negativa debe ser rechazada por validación con HTTP 422.
     */
    public function test_update_quantity_zero_or_negative_returns_422(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $cartItem = CartItem::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        // Intentar actualizar a 0
        $responseZero = $this->actingAs($user)
            ->postJson("/cart/update/{$cartItem->id}", ['quantity' => 0]);
        $responseZero->assertStatus(422);

        // Intentar actualizar a negativo (-1)
        $responseNeg = $this->actingAs($user)
            ->postJson("/cart/update/{$cartItem->id}", ['quantity' => -1]);
        $responseNeg->assertStatus(422);

        // Intentar actualizar a cadena no numérica
        $responseStr = $this->actingAs($user)
            ->postJson("/cart/update/{$cartItem->id}", ['quantity' => 'invalido']);
        $responseStr->assertStatus(422);
    }

    /**
     * Caso límite: Cruzar el umbral de 50.00 € activa automáticamente el envío gratuito en tiempo real.
     */
    public function test_realtime_update_crosses_free_shipping_threshold(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'stock' => 10,
            'price' => 24.99,
        ]);

        $cartItem = CartItem::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        // Con 1 unidad (24.99 €) -> Envío de pago, restante 25.01 €
        $step1 = $this->actingAs($user)
            ->postJson("/cart/update/{$cartItem->id}", ['quantity' => 1]);

        $step1->assertOk()
            ->assertJsonPath('totals.is_free_shipping', false)
            ->assertJsonPath('totals.remaining_for_free_shipping', 25.01)
            ->assertJsonPath('totals.shipping_progress', 49);

        // Incrementamos a 2 unidades (49.98 €) -> Justo por debajo, 99% de progreso, NO 100%
        $step2 = $this->actingAs($user)
            ->postJson("/cart/update/{$cartItem->id}", ['quantity' => 2]);

        $step2->assertOk()
            ->assertJsonPath('totals.total', 49.98)
            ->assertJsonPath('totals.is_free_shipping', false)
            ->assertJsonPath('totals.remaining_for_free_shipping', 0.02)
            ->assertJsonPath('totals.shipping_progress', 99);

        // Incrementamos a 3 unidades (74.97 €) -> Envío gratuito alcanzado
        $step3 = $this->actingAs($user)
            ->postJson("/cart/update/{$cartItem->id}", ['quantity' => 3]);

        $step3->assertOk()
            ->assertJsonPath('totals.total', 74.97)
            ->assertJsonPath('totals.is_free_shipping', true)
            ->assertJsonPath('totals.remaining_for_free_shipping', 0)
            ->assertJsonPath('totals.shipping_progress', 100);
    }

    /**
     * Eliminación de producto vía JSON devuelve los totales recalculados.
     */
    public function test_remove_item_via_json_returns_recalculated_totals(): void
    {
        $user = User::factory()->create();
        $p1 = Product::factory()->create(['stock' => 10, 'price' => 30.00]);
        $p2 = Product::factory()->create(['stock' => 10, 'price' => 25.00]);

        $item1 = CartItem::factory()->create(['user_id' => $user->id, 'product_id' => $p1->id, 'quantity' => 1]);
        $item2 = CartItem::factory()->create(['user_id' => $user->id, 'product_id' => $p2->id, 'quantity' => 1]);

        $response = $this->actingAs($user)
            ->deleteJson("/cart/remove/{$item1->id}");

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'cart_count' => 1,
                'is_empty' => false,
                'totals' => [
                    'total' => 25.00,
                    'items_count' => 1,
                ],
            ]);

        $this->assertDatabaseMissing('cart_items', ['id' => $item1->id]);
        $this->assertDatabaseHas('cart_items', ['id' => $item2->id]);
    }
}
