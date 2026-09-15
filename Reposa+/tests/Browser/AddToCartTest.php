<?php

/*
|--------------------------------------------------------------------------
| E2E Test — Añadir Producto al Carrito
|--------------------------------------------------------------------------
|
| Valida el flujo completo de añadir un producto al carrito,
| verificar el contenido del carrito, y comprobar la integridad de datos.
|
*/

use App\Models\CartItem;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function (): void {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    DB::table('cart_items')->truncate();
    DB::table('order_items')->truncate();
    DB::table('orders')->truncate();
    DB::table('refunds')->truncate();
    DB::table('favorite_product')->truncate();
    DB::table('addresses')->truncate();
    DB::table('profiles')->truncate();
    DB::table('users')->where('email', 'like', '%@example.com')->delete();
    DB::table('products')->where('name', 'like', '%Prueba%')->delete();
    DB::table('products')->where('name', 'like', '%Almohada%')->delete();
    DB::table('categories')->where('name', 'Cervical')->delete();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
});

it('can add a product to cart from catalog page', function (): void {
    $user = createTestUser();
    $product = createTestProduct([
        'name' => 'Almohada Viscoelástica Premium',
        'price' => 49.99,
        'stock' => 10,
    ]);

    $this->actingAs($user)
        ->post("/cart/add/{$product->id}", ['quantity' => 1])
        ->assertRedirect();

    $this->assertDatabaseHas('cart_items', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);
});

it('increments quantity when adding same product twice', function (): void {
    $user = createTestUser();
    $product = createTestProduct(['stock' => 10]);

    $this->actingAs($user)->post("/cart/add/{$product->id}", ['quantity' => 1]);
    $this->actingAs($user)->post("/cart/add/{$product->id}", ['quantity' => 1]);

    $cartCount = (int) CartItem::where('user_id', $user->id)->sum('quantity');
    expect($cartCount)->toBe(2);

    $this->assertDatabaseHas('cart_items', [
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 2,
    ]);
});

it('cannot add product with insufficient stock', function (): void {
    $user = createTestUser();
    $product = createTestProduct(['stock' => 1]);

    $this->actingAs($user)->post("/cart/add/{$product->id}", ['quantity' => 1]);
    $this->actingAs($user)->post("/cart/add/{$product->id}", ['quantity' => 1]);

    $cartCount = CartItem::where('user_id', $user->id)->sum('quantity');
    expect($cartCount)->toBeLessThanOrEqual(1);
});

it('can view cart page with added products', function (): void {
    $user = createTestUser();
    $product = createTestProduct([
        'name' => 'Almohada Cervical Ergonómica',
        'price' => 39.50,
        'stock' => 10,
    ]);

    createCartItem($user, $product, 2);

    $this->actingAs($user)->get('/cart')
        ->assertOk()
        ->assertSee('Almohada Cervical Ergonómica')
        ->assertSee('39.50');

    $total = $product->price * 2;
    expect($total)->toBe(79.00);
});
