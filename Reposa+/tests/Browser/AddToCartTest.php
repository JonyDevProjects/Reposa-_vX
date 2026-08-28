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

use App\Models\Product;

uses(\Tests\TestCase::class);

beforeEach(function (): void {
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');
    \Illuminate\Support\Facades\DB::table('cart_items')->truncate();
    \Illuminate\Support\Facades\DB::table('order_items')->truncate();
    \Illuminate\Support\Facades\DB::table('orders')->truncate();
    \Illuminate\Support\Facades\DB::table('refunds')->truncate();
    \Illuminate\Support\Facades\DB::table('favorite_product')->truncate();
    \Illuminate\Support\Facades\DB::table('addresses')->truncate();
    \Illuminate\Support\Facades\DB::table('profiles')->truncate();
    \Illuminate\Support\Facades\DB::table('users')->where('email', 'like', '%@example.com')->delete();
    \Illuminate\Support\Facades\DB::table('products')->where('name', 'like', '%Prueba%')->delete();
    \Illuminate\Support\Facades\DB::table('products')->where('name', 'like', '%Almohada%')->delete();
    \Illuminate\Support\Facades\DB::table('categories')->where('name', 'Cervical')->delete();
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');
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

    $cartCount = (int) \App\Models\CartItem::where('user_id', $user->id)->sum('quantity');
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

    $cartCount = \App\Models\CartItem::where('user_id', $user->id)->sum('quantity');
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
