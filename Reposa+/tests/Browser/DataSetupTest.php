<?php

/*
|--------------------------------------------------------------------------
| E2E Test — Preparación de Datos de Prueba
|--------------------------------------------------------------------------
|
| Valida que los factories y seeders crean datos correctamente
| para tests E2E. Esto es un sanity check para asegurar que
| los helpers de datos funcionan antes de ejecutar tests más complejos.
|
*/

use App\Models\Category;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
    \Illuminate\Support\Facades\DB::table('users')->where('email', 'like', '%@reposaplus.com')->delete();
    \Illuminate\Support\Facades\DB::table('products')->where('name', 'like', '%Prueba%')->delete();
    \Illuminate\Support\Facades\DB::table('products')->where('name', 'like', '%Almohada%')->delete();
    \Illuminate\Support\Facades\DB::table('categories')->where('name', 'Cervical')->delete();
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');
});

it('creates user with correct attributes', function (): void {
    // Arrange & Act
    $user = createTestUser();

    // Assert
    expect($user->name)->toBe(TEST_USER_NAME);
    expect($user->email)->toBe(TEST_USER_EMAIL);
    expect($user->role)->toBe('user');
    expect(Hash::check(TEST_USER_PASSWORD, $user->password))->toBeTrue();
    expect($user->email_verified_at)->not->toBeNull();
});

it('creates admin with correct attributes', function (): void {
    // Arrange & Act
    $admin = createTestAdmin();

    // Assert
    expect($admin->name)->toBe(TEST_ADMIN_NAME);
    expect($admin->email)->toBe(TEST_ADMIN_EMAIL);
    expect($admin->role)->toBe('admin');
});

it('creates product with valid data', function (): void {
    // Arrange & Act
    $product = createTestProduct();

    // Assert
    expect($product->name)->toBe('Almohada de Prueba');
    expect($product->price)->toBeGreaterThan(0);
    expect($product->stock)->toBeGreaterThanOrEqual(0);
    expect($product->description)->not->toBeEmpty();
});

it('creates category correctly', function (): void {
    // Arrange & Act
    $category = createTestCategory();

    // Assert
    expect($category->name)->toBe('Cervical');
    expect($category->slug)->toBe('cervical');
});

it('creates order with items', function (): void {
    // Arrange
    $user = createTestUser();
    $product1 = createTestProduct(['price' => 29.99, 'stock' => 10]);
    $product2 = createTestProduct(['price' => 49.99, 'stock' => 5]);

    // Act
    $order = createTestOrder($user, [
        ['price' => 29.99, 'stock' => 10, 'quantity' => 2],
        ['price' => 49.99, 'stock' => 5, 'quantity' => 1],
    ]);

    // Assert
    expect($order->user_id)->toBe($user->id);
    expect($order->status)->toBe('completed');
    expect($order->total_amount)->toBe(109.97); // 29.99*2 + 49.99*1

    $orderItems = $order->orderItems()->get();
    expect($orderItems->count())->toBe(2);
});

it('creates cart item correctly', function (): void {
    // Arrange
    $user = createTestUser();
    $product = createTestProduct();

    // Act
    $cartItem = createCartItem($user, $product, 3);

    // Assert
    expect($cartItem->user_id)->toBe($user->id);
    expect($cartItem->product_id)->toBe($product->id);
    expect($cartItem->quantity)->toBe(3);
});

it('creates address correctly', function (): void {
    // Arrange
    $user = createTestUser();

    // Act
    $address = createTestAddress($user);

    // Assert
    expect($address->user_id)->toBe($user->id);
    expect($address->street)->toBe('Calle de Prueba 123');
    expect($address->city)->toBe('Madrid');
    expect($address->zip_code)->toBe('28001');
    expect($address->is_main)->toBeTrue();
});

it('prepares complete cart scenario', function (): void {
    // Arrange & Act
    $scenario = prepareCartScenario();

    // Assert
    expect($scenario['user'])->toBeInstanceOf(User::class);
    expect($scenario['product'])->toBeInstanceOf(Product::class);
    expect(\App\Models\CartItem::where('user_id', $scenario['user']->id)->count())->toBe(1);
    expect($scenario['product']->price)->toBe(49.99);
});

it('prepares complete order scenario', function (): void {
    // Arrange & Act
    $scenario = prepareOrderScenario('completed');

    // Assert
    expect($scenario['admin'])->toBeInstanceOf(User::class);
    expect($scenario['admin']->role)->toBe('admin');
    expect($scenario['user'])->toBeInstanceOf(User::class);
    expect($scenario['order'])->toBeInstanceOf(Order::class);
    expect($scenario['order']->status)->toBe('completed');
});

it('seeds database correctly with artisan', function (): void {
    // Clean ALL data before seeding to avoid any duplicates
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0');
    \Illuminate\Support\Facades\DB::table('category_product')->truncate();
    \Illuminate\Support\Facades\DB::table('categories')->truncate();
    \Illuminate\Support\Facades\DB::table('products')->truncate();
    \Illuminate\Support\Facades\DB::table('users')->where('email', 'like', '%@reposaplus.com')->delete();
    \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1');

    // Act
    $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);

    // Assert — Verificar datos semilla
    expect(User::count())->toBeGreaterThanOrEqual(2);
    expect(Category::count())->toBeGreaterThanOrEqual(1);
    expect(Product::count())->toBeGreaterThanOrEqual(1);
});
