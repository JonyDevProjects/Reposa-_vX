<?php

/*
|--------------------------------------------------------------------------
| Browser Test Helpers
|--------------------------------------------------------------------------
|
| Funciones auxiliares reutilizables para tests E2E.
| Estas funciones manejan la preparación de datos, login, y operaciones
| comunes que se repiten en múltiples tests.
|
*/

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem;
use App\Models\Address;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Configuracion del Navegador
|--------------------------------------------------------------------------
|
| Se aplica el TestCase de Laravel a todos los tests del directorio Browser.
| Esto asegura que el contenedor de Laravel se bootstrap correctamente.
|
*/

/*
|--------------------------------------------------------------------------
| URL Base para tests E2E
|--------------------------------------------------------------------------
*/

define('APP_BASE_URL', env('E2E_BASE_URL', 'http://localhost:8080'));

/*
|--------------------------------------------------------------------------
| Datos de Test Constantes
|--------------------------------------------------------------------------
*/

/** Credenciales del usuario de prueba */
define('TEST_USER_EMAIL', 'test@example.com');
define('TEST_USER_PASSWORD', 'password');
define('TEST_USER_NAME', 'Test User');

/** Credenciales del admin de prueba */
define('TEST_ADMIN_EMAIL', 'admin@example.com');
define('TEST_ADMIN_PASSWORD', 'password');
define('TEST_ADMIN_NAME', 'Test Admin');

/*
|--------------------------------------------------------------------------
| Factory Helpers — Crear datos de prueba para tests E2E
|--------------------------------------------------------------------------
*/

/**
 * Crear usuario de prueba con perfil y dirección.
 */
function createTestUser(array $overrides = []): User
{
    $defaults = [
        'name' => TEST_USER_NAME,
        'email' => TEST_USER_EMAIL,
        'password' => Hash::make(TEST_USER_PASSWORD),
        'role' => 'user',
    ];

    $data = array_merge($defaults, $overrides);
    $existing = User::where('email', $data['email'])->first();

    if ($existing) {
        $existing->update($data);
        return $existing->fresh();
    }

    return User::factory()->create($data);
}

/**
 * Crear admin de prueba.
 */
function createTestAdmin(array $overrides = []): User
{
    $defaults = [
        'name' => TEST_ADMIN_NAME,
        'email' => TEST_ADMIN_EMAIL,
        'password' => Hash::make(TEST_ADMIN_PASSWORD),
        'role' => 'admin',
    ];

    $data = array_merge($defaults, $overrides);
    $existing = User::where('email', $data['email'])->first();

    if ($existing) {
        $existing->update($data);
        return $existing->fresh();
    }

    return User::factory()->create($data);
}

/**
 * Crear producto con stock disponible.
 */
function createTestProduct(array $overrides = []): Product
{
    return Product::factory()->create(array_merge([
        'name' => 'Almohada de Prueba',
        'price' => 49.99,
        'stock' => 10,
        'description' => 'Producto de prueba para tests E2E',
    ], $overrides));
}

/**
 * Crear categoría de prueba.
 */
function createTestCategory(array $overrides = []): Category
{
    $defaults = [
        'name' => 'Cervical',
        'slug' => 'cervical',
    ];

    $data = array_merge($defaults, $overrides);
    $existing = Category::where('slug', $data['slug'])->first();

    if ($existing) {
        $existing->update($data);
        return $existing->fresh();
    }

    return Category::factory()->create($data);
}

/**
 * Crear pedido completo con items.
 */
function createTestOrder(User $user, array $products, string $status = 'completed'): Order
{
    $total = 0;
    $order = Order::factory()->create([
        'user_id' => $user->id,
        'status' => $status,
    ]);

    foreach ($products as $productData) {
        $quantity = is_array($productData) ? ($productData['quantity'] ?? 1) : 1;

        $product = is_array($productData)
            ? Product::factory()->create(collect($productData)->except('quantity')->toArray())
            : $productData;

        $priceAtPurchase = $product->price;

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price_at_purchase' => $priceAtPurchase,
        ]);

        $total += $priceAtPurchase * $quantity;
    }

    $order->update(['total_amount' => $total]);
    return $order;
}

/**
 * Crear items en el carrito para un usuario.
 */
function createCartItem(User $user, Product $product, int $quantity = 1): CartItem
{
    return CartItem::factory()->create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => $quantity,
    ]);
}

/**
 * Crear dirección para un usuario.
 */
function createTestAddress(User $user, array $overrides = []): Address
{
    return Address::create(array_merge([
        'user_id' => $user->id,
        'street' => 'Calle de Prueba 123',
        'city' => 'Madrid',
        'zip_code' => '28001',
        'is_main' => true,
    ], $overrides));
}

/*
|--------------------------------------------------------------------------
| Auth Helpers — Login en el navegador
|--------------------------------------------------------------------------
*/

/**
 * Realizar login como usuario a través del formulario de login.
 */
function loginAsUser($page): void
{
    $page->goto(APP_BASE_URL . '/login')
        ->fill('#email', TEST_USER_EMAIL)
        ->fill('#password', TEST_USER_PASSWORD)
        ->press('button[type="submit"]');
}

/**
 * Realizar login como admin a través del formulario de login.
 */
function loginAsAdmin($page): void
{
    $page->goto(APP_BASE_URL . '/login')
        ->fill('#email', TEST_ADMIN_EMAIL)
        ->fill('#password', TEST_ADMIN_PASSWORD)
        ->press('button[type="submit"]');
}

/*
|--------------------------------------------------------------------------
| Carrito Helpers — Operaciones comunes del carrito
|--------------------------------------------------------------------------
*/

/**
 * Añadir un producto al carrito desde la página de catálogo.
 */
function addProductToCart($page, Product $product): void
{
    $page->goto(APP_BASE_URL . "/catalog/{$product->id}")
        ->press('form[action*="/cart/add/"] button[type="submit"]')
        ->wait();
}

/*
|--------------------------------------------------------------------------
| Stripe Mock Helpers — Simular respuestas de Stripe
|--------------------------------------------------------------------------
*/

/**
 * Mockear respuestas de Stripe para tests E2E.
 * Intercepta las llamadas HTTP a Stripe y devuelve respuestas simuladas.
 */
function mockStripeResponses(): void
{
    Http::fake([
        // Mock checkout session creation
        'api.stripe.com/v1/checkout/sessions' => Http::response([
            'id' => 'cs_test_' . Str::random(24),
            'url' => APP_BASE_URL . '/checkout/stripe/success?session_id=cs_test_mock',
            'payment_status' => 'paid',
            'payment_intent' => 'pi_test_' . Str::random(24),
            'metadata' => ['order_id' => null], // Se setea dinámicamente
        ], 200),

        // Mock checkout session retrieval
        'api.stripe.com/v1/checkout/sessions/*' => Http::response([
            'id' => 'cs_test_mock',
            'payment_status' => 'paid',
            'metadata' => ['order_id' => null],
        ], 200),

        // Mock customer creation
        'api.stripe.com/v1/customers' => Http::response([
            'id' => 'cus_test_' . Str::random(14),
        ], 200),
    ]);
}

/*
|--------------------------------------------------------------------------
| MailHog Helpers — Validar correos electrónicos
|--------------------------------------------------------------------------
*/

/**
 * Obtener el último correo enviado a MailHog.
 *
 * @param string $to Email del destinatario
 * @param int $timeoutSeconds Tiempo máximo de espera
 * @return array|null Datos del correo o null si no se encontró
 */
function getLatestMailhogEmail(string $to, int $timeoutSeconds = 5): ?array
{
    $mailhogUrl = env('MAILHOG_URL', 'http://localhost:8025');

    $start = time();
    while (time() - $start < $timeoutSeconds) {
        $response = Http::timeout(2)->get("{$mailhogUrl}/api/v2/search", [
            'kind' => 'to',
            'query' => $to,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['items'][0])) {
                return $data['items'][0];
            }
        }

        usleep(500000); // 0.5 seconds
    }

    return null;
}

/**
 * Verificar si se envió un correo con un asunto específico.
 */
function assertMailhogHasEmail(string $to, string $subject): bool
{
    $mailhogUrl = env('MAILHOG_URL', 'http://localhost:8025');

    $response = Http::timeout(2)->get("{$mailhogUrl}/api/v2/search", [
        'kind' => 'to',
        'query' => $to,
    ]);

    if (! $response->successful()) {
        return false;
    }

    $data = $response->json();
    $items = $data['items'] ?? [];

    foreach ($items as $item) {
        $emailSubject = $item['Content']['Headers']['Subject'][0] ?? '';
        if (str_contains($emailSubject, $subject)) {
            return true;
        }
    }

    return false;
}

/**
 * Limpiar todos los correos de MailHog.
 */
function flushMailhog(): void
{
    $mailhogUrl = env('MAILHOG_URL', 'http://localhost:8025');
    Http::timeout(2)->delete("{$mailhogUrl}/api/v1/messages");
}

/*
|--------------------------------------------------------------------------
| Webhook Helpers — Simular webhooks de Stripe
|--------------------------------------------------------------------------
*/

/**
 * Enviar un webhook simulado de Stripe al endpoint de la aplicación.
 */
function sendStripeWebhook(string $eventType, array $data): \Symfony\Component\HttpFoundation\Response
{
    $payload = [
        'id' => 'evt_test_' . Str::random(24),
        'type' => $eventType,
        'data' => [
            'object' => $data,
        ],
        'created' => now()->timestamp,
        'livemode' => false,
    ];

    $secret = env('STRIPE_WEBHOOK_SECRET', 'whsec_test_placeholder');
    $timestamp = (string) now()->timestamp;
    $signedPayload = $timestamp . '.' . json_encode($payload);
    $signature = hash_hmac('sha256', $signedPayload, $secret);

    return test()->post('/stripe/webhook', $payload, [
        'Stripe-Signature' => "t={$timestamp},v1={$signature}",
        'Content-Type' => 'application/json',
    ]);
}

/**
 * Preparar escenario completo: usuario + producto en carrito.
 */
function prepareCartScenario(): array
{
    $user = createTestUser();
    $product = createTestProduct(['stock' => 10, 'price' => 49.99]);
    createCartItem($user, $product, 2);

    return compact('user', 'product');
}

/**
 * Preparar escenario completo: admin + usuario + pedido con items.
 */
function prepareOrderScenario(string $status = 'completed'): array
{
    $admin = createTestAdmin();
    $user = createTestUser();
    $product = createTestProduct(['price' => 75.00, 'stock' => 20]);
    $order = Order::factory()->create([
        'user_id' => $user->id,
        'total_amount' => 150.00,
        'status' => $status,
    ]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 2,
        'price_at_purchase' => 75.00,
    ]);

    return compact('admin', 'user', 'product', 'order');
}
