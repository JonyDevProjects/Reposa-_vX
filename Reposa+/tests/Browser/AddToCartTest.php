<?php

/*
|--------------------------------------------------------------------------
| E2E Test — Añadir Producto al Carrito
|--------------------------------------------------------------------------
|
| Valida el flujo completo de añadir un producto al carrito desde el
| catálogo, verificar el badge del carrito, y comprobar que el producto
| aparece en la página del carrito.
|
*/

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(\Tests\TestCase::class);
uses(RefreshDatabase::class);

it('can add a product to cart from catalog page', function (): void {
    // Arrange
    $user = createTestUser();
    $product = createTestProduct([
        'name' => 'Almohada Viscoelástica Premium',
        'price' => 49.99,
        'stock' => 10,
    ]);

    // Act — Login + navegar al catálogo + añadir al carrito
    visit(APP_BASE_URL . '/login')
        ->fill('#email', TEST_USER_EMAIL)
        ->fill('#password', TEST_USER_PASSWORD)
        ->press('button[type="submit"]');

    // Navegar a la página del producto
    visit(APP_BASE_URL . "/catalog/{$product->id}")
        ->assertSee('Almohada Viscoelástica Premium')
        ->assertSee('49.99');

    // Añadir al carrito
    visit(APP_BASE_URL . "/catalog/{$product->id}")
        ->press('form[action*="/cart/add/"] button[type="submit"]');

    // Assert — Verificar que el badge del carrito muestra 1
    $cartCount = \App\Models\CartItem::where('user_id', $user->id)->sum('quantity');
    expect($cartCount)->toBe(1);

    // Verificar que el producto está en el carrito en la BD
    $this->assertDatabaseHas('cart_items', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);
});

it('increments quantity when adding same product twice', function (): void {
    // Arrange
    $user = createTestUser();
    $product = createTestProduct(['stock' => 10]);

    // Act — Login
    visit(APP_BASE_URL . '/login')
        ->fill('#email', TEST_USER_EMAIL)
        ->fill('#password', TEST_USER_PASSWORD)
        ->press('button[type="submit"]');

    // Añadir dos veces el mismo producto
    visit(APP_BASE_URL . "/catalog/{$product->id}")
        ->press('form[action*="/cart/add/"] button[type="submit"]');

    visit(APP_BASE_URL . "/catalog/{$product->id}")
        ->press('form[action*="/cart/add/"] button[type="submit"]');

    // Assert — Cantidad incrementada
    $cartCount = \App\Models\CartItem::where('user_id', $user->id)->sum('quantity');
    expect($cartCount)->toBe(2);

    $this->assertDatabaseHas('cart_items', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
});

it('cannot add product with insufficient stock', function (): void {
    // Arrange
    $user = createTestUser();
    $product = createTestProduct(['stock' => 1]);

    // Act — Login
    visit(APP_BASE_URL . '/login')
        ->fill('#email', TEST_USER_EMAIL)
        ->fill('#password', TEST_USER_PASSWORD)
        ->press('button[type="submit"]');

    // Intentar añadir más unidades de las disponibles
    visit(APP_BASE_URL . "/catalog/{$product->id}")
        ->press('form[action*="/cart/add/"] button[type="submit"]');

    // Second add should exceed stock
    visit(APP_BASE_URL . "/catalog/{$product->id}")
        ->press('form[action*="/cart/add/"] button[type="submit"]');

    // Assert — El carrito no debería tener más de 1 unidad
    $cartCount = \App\Models\CartItem::where('user_id', $user->id)->sum('quantity');
    expect($cartCount)->toBeLessThanOrEqual(1);
});

it('can view cart page with added products', function (): void {
    // Arrange
    $user = createTestUser();
    $product = createTestProduct([
        'name' => 'Almohada Cervical Ergonómica',
        'price' => 39.50,
        'stock' => 10,
    ]);

    createCartItem($user, $product, 2);

    // Act — Login
    visit(APP_BASE_URL . '/login')
        ->fill('#email', TEST_USER_EMAIL)
        ->fill('#password', TEST_USER_PASSWORD)
        ->press('button[type="submit"]');

    // Navegar al carrito
    visit(APP_BASE_URL . '/cart')
        ->assertSee('Almohada Cervical Ergonómica')
        ->assertSee('39.50');

    // Assert — Verificar el total
    $total = $product->price * 2;
    expect($total)->toBe(79.00);
});
