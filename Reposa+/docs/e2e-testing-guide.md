# Guia de Tests E2E con Pest + Playwright — Reposa+

## Indice

1. [Arquitectura y Decisiones Tecnicas](#1-arquitectura-y-decisiones-tecnicas)
2. [Requisitos Previos](#2-requisitos-previos)
3. [Instalacion y Configuracion](#3-instalacion-y-configuracion)
4. [Estructura del Proyecto](#4-estructura-del-proyecto)
5. [Ejecucion de Tests](#5-ejecucion-de-tests)
6. [Flujos de Prueba Implementados](#6-flujos-de-prueba-implementados)
7. [Simulacion de Stripe](#7-simulacion-de-stripe)
8. [MailHog — Validacion de Correos](#8-mailhog--validacion-de-correos)
9. [Datos de Prueba con Factories](#9-datos-de-prueba-con-factories)
10. [Docker para E2E](#10-docker-para-e2e)
11. [Buenas Practicas](#11-buenas-practicas)
12. [Troubleshooting](#12-troubleshooting)

---

## 1. Arquitectura y Decisiones Tecnicas

### Stack E2E

```
Pest PHP v4.7  →  pest-plugin-browser  →  Playwright (Chromium)
       ↓                                        ↓
  Laravel App  ←→  MySQL/SQLite  ←→  MailHog (SMTP)
```

### Por que Pest + Playwright (no Jest/Playwright)

| Aspecto | Pest + Playwright | Jest + Playwright |
|---------|-------------------|-------------------|
| Lenguaje | PHP (mismo que Laravel) | JavaScript/TypeScript |
| DB access | Directo via Eloquent | Requiere HTTP o API |
| Factories | `User::factory()->create()` | Fetch API o seeders |
| Mail fake | `Mail::fake()` nativo | Requiere mock manual |
| Assertions | Pest `expect()` | Jest `expect()` |

**Decision clave**: Los tests E2E en PHP permiten usar los mismos factories, modelos y helpers de Laravel que ya existen. No hace falta duplicar la logica de seed en JavaScript.

### Que testea cada nivel

| Nivel | Que cubre | Velocidad |
|-------|-----------|-----------|
| **Unit** | Modelos, helpers puros | ~1ms |
| **Feature** | Controllers, rutas, middleware | ~50ms |
| **Browser (E2E)** | UI completa + DB + servicios | ~2-10s |

---

## 2. Requisitos Previos

### Software necesario

```bash
# PHP 8.3+ con extensiones necesarias
php -v  # >= 8.3

# Composer
composer -V  # >= 2.x

# Node.js 18+ con npm
node -v  # >= 18
npm -v   # >= 9

# Docker (opcional, para stack aislado)
docker --version
docker compose version
```

### Base de datos de pruebas

Opcion local (SQLite, mas rapida):
```bash
touch database/database.sqlite
```

Opcion Docker (MySQL, mas realista):
```bash
docker compose -f docker-compose.e2e.yml up -d mysql
```

### MailHog (validacion de correos)

```bash
# macOS con Homebrew
brew install mailhog
brew services start mailhog

# O via Docker
docker run -d -p 1025:1025 -p 8025:8025 mailhog/mailhog
```

Acceso a la interfaz web: `http://localhost:8025`

---

## 3. Instalacion y Configuracion

### Paso 1: Instalar Pest + Browser Plugin

```bash
composer require --dev pestphp/pest pestphp/pest-plugin-browser
```

Esto instala:
- `pestphp/pest` v4.7 — Framework de testing
- `pestphp/pest-plugin-browser` v4.3 — Integracion con Playwright

**Nota**: Pest v4 requiere que cada archivo de tests tenga `uses(\Tests\TestCase::class)` 
al inicio para bootstrear el contenedor de Laravel. Este binding esta en cada archivo de test.

### Paso 2: Instalar Playwright

```bash
npm install --save-dev @playwright/test
npx playwright install chromium
```

### Paso 3: Configurar .env.testing

Copiar `.env.testing` a la raiz del proyecto. Configuracion clave:

```env
APP_ENV=testing
DB_CONNECTION=sqlite          # o mysql para tests realistas
DB_DATABASE=:memory:          # o reposaplus_e2e
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025                # MailHog SMTP
E2E_BASE_URL=http://localhost:8080
MAILHOG_URL=http://localhost:8025
```

### Paso 4: Configurar phpunit.xml

Se ha anadido el testsuite `Browser`:

```xml
<testsuite name="Browser">
    <directory>tests/Browser</directory>
    <suffix>Pest.php</suffix>
</testsuite>
```

---

## 4. Estructura del Proyecto

```
tests/
├── Pest.php                   # Config Pest global ( Feature + Unit)
├── Browser/
│   ├── helpers.php            # Funciones auxiliares reutilizables
│   ├── AddToCartTest.php      # Anadir producto al carrito
│   ├── CheckoutTest.php       # Proceso de checkout completo
│   ├── FailedPaymentTest.php  # Pago fallido
│   ├── ViewOrderUserTest.php  # Ver pedido (usuario)
│   ├── AdminOrderTest.php     # Ver pedido (admin)
│   ├── StripeWebhookTest.php  # Webhooks de Stripe
│   ├── MailHogTest.php        # Validacion de correos
│   └── DataSetupTest.php      # Sanity check de factories
├── Feature/                   # Tests existentes (HTTP)
├── Unit/                      # Tests existentes (unidad)
└── TestCase.php               # Base TestCase de Laravel
```

**Importante**: Cada archivo de test en `Browser/` incluye `uses(\Tests\TestCase::class)` 
al inicio para bootstrear el contenedor de Laravel. Sin esto, los tests fallan con 
"Target class [config] does not exist".

### Archivos de configuracion

| Archivo | Proposito |
|---------|-----------|
| `tests/Pest.php` | Config Pest global ( Feature/Unit) |
| `tests/Browser/helpers.php` | Funciones reutilizables (login, crear datos, etc.) |
| `.env.testing` | Variables de entorno para tests E2E |
| `phpunit.xml` | Suite `Browser` anadida |
| `docker-compose.e2e.yml` | Stack Docker para E2E aislado |
| `package.json` | Scripts `test:e2e*` |

---

## 5. Ejecucion de Tests

### Comandos rapidos

```bash
# Ejecutar todos los tests E2E
npm run test:e2e
# Equivalente: npx pest --testsuite=Browser

# Ver el navegador (modo headed)
npm run test:e2e:headed

# Debug — pausa en cada paso
npm run test:e2e:debug

# Filtrar por nombre de test
npx pest --testsuite=Browser --filter="add product"
npx pest --testsuite=Browser --filter="AddToCartTest"

# Un solo test
npx pest --testsuite=Browser --filter="checkout"
```

### Comandos artisan

```bash
# Tests E2E via artisan
php artisan test --testsuite=Browser

# Con filtro
php artisan test --testsuite=Browser --filter="AddToCartTest"
```

### Flujo completo con Docker

```bash
# 1. Levantar stack completo
docker compose -f docker-compose.e2e.yml up -d

# 2. Ejecutar tests
docker compose -f docker-compose.e2e.yml exec app php artisan test --testsuite=Browser

# 3. Ver resultados
docker compose -f docker-compose.e2e.yml logs app

# 4. Limpiar
docker compose -f docker-compose.e2e.yml down -v
```

---

## 6. Flujos de Prueba Implementados

### 6.1 Anadir Producto al Carrito (`AddToCartTest.php`)

```php
it('can add a product to cart from catalog page', function (): void {
    $user = createTestUser();
    $product = createTestProduct(['name' => 'Almohada Viscoelastica', 'stock' => 10]);

    // Login + navegar al producto + anadir al carrito
    visit(APP_BASE_URL . '/login')
        ->fill('#email', TEST_USER_EMAIL)
        ->fill('#password', TEST_USER_PASSWORD)
        ->press('button[type="submit"]');

    visit(APP_BASE_URL . "/catalog/{$product->id}")
        ->press('form[action*="/cart/add/"] button[type="submit"]');

    // Verificar en BD
    $this->assertDatabaseHas('cart_items', [
        'user_id' => $user->id,
        'product_id' => $product->id,
    ]);
});
```

### 6.2 Checkout Completo (`CheckoutTest.php`)

```php
it('completes checkout successfully', function (): void {
    Mail::fake();
    $user = createTestUser();
    $product = createTestProduct(['stock' => 10, 'price' => 45.99]);
    createCartItem($user, $product, 2);

    // Login + checkout
    $this->actingAs($user)->post('/checkout')
        ->assertRedirect('/profile#orders');

    // Verificar pedido + stock
    expect($product->fresh()->stock)->toBe(8);
    $this->assertDatabaseHas('orders', [
        'user_id' => $user->id,
        'status' => 'pending',
    ]);
});
```

### 6.3 Pago Fallido (`FailedPaymentTest.php`)

```php
it('handles payment failure webhook', function (): void {
    $user = createTestUser();
    $order = Order::factory()->pending()->create(['user_id' => $user->id]);

    // Enviar webhook de fallo
    $payload = [
        'type' => 'payment_intent.payment_failed',
        'data' => ['object' => [
            'id' => $order->payment_intent_id,
            'last_payment_error' => ['message' => 'Card declined'],
        ]],
    ];

    test()->post('/stripe/webhook', $payload);

    // Pedido sigue pendiente
    expect($order->fresh()->status)->toBe('pending');
});
```

### 6.4 Ver Pedido Usuario (`ViewOrderUserTest.php`)

```php
it('can view order detail', function (): void {
    $user = createTestUser();
    $order = Order::factory()->completed()->create(['user_id' => $user->id]);

    visit(APP_BASE_URL . '/login')
        ->fill('#email', TEST_USER_EMAIL)
        ->fill('#password', TEST_USER_PASSWORD)
        ->press('button[type="submit"]');

    visit(APP_BASE_URL . "/orders/{$order->id}")
        ->assertSee('Completado');
});

it('cannot view other users order', function (): void {
    $user = createTestUser();
    $otherUser = User::factory()->create();
    $order = Order::factory()->create(['user_id' => $otherUser->id]);

    $this->actingAs($user)->get("/orders/{$order->id}")
        ->assertStatus(403);
});
```

### 6.5 Ver Pedido Admin (`AdminOrderTest.php`)

```php
it('admin can update order status', function (): void {
    $admin = createTestAdmin();
    $order = Order::factory()->pending()->create();

    $this->actingAs($admin)->patch("/admin/orders/{$order->id}/status", [
        'status' => 'processing',
    ]);

    expect($order->fresh()->status)->toBe('processing');
});

it('non-admin cannot access admin panel', function (): void {
    $user = createTestUser();

    $this->actingAs($user)->get('/admin/orders')
        ->assertRedirect('/');
});
```

### 6.6 Webhooks Stripe (`StripeWebhookTest.php`)

```php
it('handles checkout.session.completed', function (): void {
    $order = Order::factory()->pending()->create();
    $product = createTestProduct(['stock' => 10]);

    OrderItem::factory()->create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'quantity' => 1,
    ]);

    $payload = [
        'type' => 'checkout.session.completed',
        'data' => ['object' => [
            'payment_status' => 'paid',
            'metadata' => ['order_id' => $order->id],
        ]],
    ];

    test()->post('/stripe/webhook', $payload);

    expect($order->fresh()->status)->toBe('completed');
    expect($product->fresh()->stock)->toBe(9);
});
```

### 6.7 MailHog (`MailHogTest.php`)

```php
it('sends order confirmation email', function (): void {
    Mail::fake();
    $user = createTestUser();
    $product = createTestProduct(['price' => 55.00]);

    createCartItem($user, $product, 1);
    $this->actingAs($user)->post('/checkout');

    Mail::assertSent(OrderConfirmed::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});
```

---

## 7. Simulacion de Stripe

### Estrategia: HTTP Fakes + Webhooks Directos

Los tests E2E no interactuan con la API real de Stripe. En su lugar:

1. **Para checkout exitoso**: Se usa `Http::fake()` para simular la respuesta de Stripe
2. **Para checkout fallido**: Se simula la cancelacion (`/checkout/stripe/cancel`)
3. **Para webhooks**: Se envia el payload directamente a `POST /stripe/webhook`

### Mock de respuestas de Stripe

```php
Http::fake([
    'api.stripe.com/v1/checkout/sessions' => Http::response([
        'id' => 'cs_test_' . Str::random(24),
        'url' => route('stripe.success') . '?session_id=cs_test_mock',
        'payment_status' => 'paid',
        'payment_intent' => 'pi_test_' . Str::random(24),
    ], 200),
]);
```

### Enviar webhook simulado

```php
$payload = [
    'id' => 'evt_test_' . Str::random(24),
    'type' => 'checkout.session.completed',
    'data' => ['object' => [
        'id' => 'cs_test_session',
        'payment_status' => 'paid',
        'metadata' => ['order_id' => $order->id],
    ]],
];

test()->post('/stripe/webhook', $payload, [
    'Content-Type' => 'application/json',
]);
```

### Stripe CLI (desarrollo local)

Para tests en desarrollo con Stripe real:

```bash
# Instalar Stripe CLI
brew install stripe/stripe-cli/stripe

# Login
stripe login

# Escuchar webhooks
stripe listen --forward-to localhost:8080/stripe/webhook
```

---

## 8. MailHog — Validacion de Correos

### Configuracion

En `.env.testing`:
```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
```

### Uso en tests

```php
// Capturar correos con Mail::fake()
Mail::fake();
$this->actingAs($user)->post('/checkout');

Mail::assertSent(OrderConfirmed::class, function ($mail) {
    return $mail->hasTo('test@example.com')
        && str_contains($mail->build()->subject, 'Tu pedido');
});
```

### Verificar via API de MailHog

```php
// Obtener correos de MailHog
$response = Http::get('http://localhost:8025/api/v2/search', [
    'kind' => 'to',
    'query' => 'test@example.com',
]);

$emails = $response->json('items', []);
expect($emails)->not->toBeEmpty();
```

---

## 9. Datos de Prueba con Factories

### Helpers disponibles (`tests/Browser/helpers.php`)

```php
// Usuarios
$user = createTestUser();                          // Usuario normal
$admin = createTestAdmin();                        // Administrador
$user = createTestUser(['email' => 'custom@test.com']); // Override

// Productos
$product = createTestProduct(['price' => 29.99, 'stock' => 5]);
$category = createTestCategory(['name' => 'Látex']);

// Pedidos
$order = createTestOrder($user, [
    ['price' => 49.99, 'stock' => 10, 'quantity' => 2],
], 'completed');

// Carrito
createCartItem($user, $product, 3);

// Direcciones
createTestAddress($user, ['city' => 'Barcelona']);

// Escenarios completos
$scenario = prepareCartScenario();   // user + product + cart item
$scenario = prepareOrderScenario();  // admin + user + order + items
```

### Factories existentes

| Factory | Modelo | Estados disponibles |
|---------|--------|---------------------|
| `UserFactory` | `User` | `unverified()` |
| `ProductFactory` | `Product` | — |
| `OrderFactory` | `Order` | `pending()`, `completed()`, `cancelled()`, `refunded()`, `withPaymentIntent()` |
| `OrderItemFactory` | `OrderItem` | — |
| `CartItemFactory` | `CartItem` | — |
| `CategoryFactory` | `Category` | — |

---

## 10. Docker para E2E

### Stack completo

```bash
# Levantar todo
docker compose -f docker-compose.e2e.yml up -d

# Verificar que todo esta healthy
docker compose -f docker-compose.e2e.yml ps

# Ejecutar tests dentro del contenedor
docker compose -f docker-compose.e2e.yml exec app php artisan test --testsuite=Browser

# Ver logs
docker compose -f docker-compose.e2e.yml logs -f app

# Limpiar
docker compose -f docker-compose.e2e.yml down -v
```

### Servicios incluidos

| Servicio | Puerto | Proposito |
|----------|--------|-----------|
| MySQL 8.0 | 3307 | Base de datos |
| Redis 7 | 6380 | Cache + sesiones |
| MailHog | 8025 (web) / 1025 (SMTP) | Captura de correos |
| Laravel App | 8080 | Servidor de aplicacion |

---

## 11. Buenas Practicas

### 1. Aislamiento

- Cada test usa `RefreshDatabase` para=DB limpia
- `Mail::fake()` para no enviar correos reales
- `Http::fake()` para no llamar a APIs externas
- Datos creados via factories (no dependen de seeders)

### 2. Velocidad

- Usar SQLite `:memory:` para tests rapidos
- MySQL/Docker solo para tests que requieran features especificas de MySQL
- `Mail::fake()` es mucho mas rapido que MailHog real
- Evitar `sleep()` — usar `waitFor()` del navegador

### 3. Flaky Tests

- Usar `waitForLoadState()` despues de navegaciones
- Esperar a que los elementos existan antes de interactuar
- Usar `assertSee()` con timeouts implicitos del plugin
- No depender de orden de ejecucion

### 4. Selectores CSS

```php
// Preferir: IDs y data-attributes
$page->fill('#email', 'test@test.com')
     ->press('button[type="submit"]');

// Evitar: selectores fragiles
$page->click('.btn-primary:nth-child(2)');
```

### 5. Nomenclatura

```php
// Test names descriptivos en ingles
it('can add a product to cart from catalog', ...);
it('prevents checkout with empty cart', ...);
it('sends order confirmation email after checkout', ...);
```

### 6. Organizacion

- Un archivo por flujo principal
- Helpers compartidos en `helpers.php`
- `Pest.php` solo para configuracion global
- No mezclar tests de browser con tests HTTP

### 7. CI/CD

```yaml
# GitHub Actions example
- name: Run E2E Tests
  run: |
    docker compose -f docker-compose.e2e.yml up -d
    sleep 10
    docker compose exec app php artisan test --testsuite=Browser
  env:
    STRIPE_SECRET: ${{ secrets.STRIPE_TEST_SECRET }}
```

---

## 12. Troubleshooting

### Playwright no encuentra Chromium

```bash
npx playwright install chromium
```

### Servidor Laravel no responde

```bash
# Verificar que corre en el puerto correcto
php artisan serve --port=8080

# O verificar con curl
curl http://localhost:8080
```

### MailHog no recibe correos

```bash
# Verificar que corre
curl http://localhost:8025/api/v2/search?kind=to&query=test

# Verificar configuracion SMTP en .env.testing
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
```

### Tests fallan por timeout

```php
// Aumentar timeout globalmente en Pest.php
pest()->configure()->timeout(30000); // 30 segundos
```

### Errores de BD en tests

```bash
# Verificar migraciones
php artisan migrate:fresh --env=testing

# Verificar que .env.testing esta configurado
cat .env.testing | grep DB_
```

### Stripe webhooks no funcionan

```bash
# Verificar el secret
echo $STRIPE_WEBHOOK_SECRET

# Verificar la ruta
php artisan route:list --name=stripe
```
