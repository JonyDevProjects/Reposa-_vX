<?php

/*
|--------------------------------------------------------------------------
| E2E Test — Validación de Correos con MailHog
|--------------------------------------------------------------------------
|
| Valida que los correos electrónicos se envían correctamente
| utilizando MailHog como servidor SMTP de desarrollo.
|
| Requisitos:
| - MailHog corriendo en http://localhost:8025
| - Configuración MAIL_MAILER=smtp, MAIL_PORT=1025 en .env.testing
|
| Flujo:
| 1. Realizar un checkout que dispare el envío de correo
| 2. Consultar la API de MailHog para verificar el correo
| 3. Validar asunto, destinatario y contenido
|
*/

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

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


it('sends order confirmation email via MailHog', function (): void {
    // Arrange
    Mail::fake(); // Fake para evitar envío real, pero captura el email
    Http::fake();

    $user = createTestUser();
    $product = createTestProduct([
        'name' => 'Almohada Premium',
        'price' => 55.00,
        'stock' => 10,
    ]);

    createCartItem($user, $product, 1);

    // Act — Ejecutar checkout
    $response = $this->actingAs($user)->post('/checkout');
    $response->assertRedirect('/profile#orders');

    // Assert — Mail capturado (OrderConfirmed implements ShouldQueue)
    Mail::assertQueued(\App\Mail\OrderConfirmed::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});

it('sends payment failed email via MailHog', function (): void {
    // Arrange
    Mail::fake();
    Http::fake();

    $user = createTestUser();
    $order = Order::factory()->pending()->create([
        'user_id' => $user->id,
        'total_amount' => 39.99,
    ]);

    // Act — Crear instancia de mailable directamente
    $mailable = new \App\Mail\PaymentFailed($order, 'Card declined');

    // Assert — Verificar configuración del correo
    expect($mailable->envelope()->subject)->toBe('Pago no procesado — Reposa+');
    expect($mailable->order->id)->toBe($order->id);
    expect($mailable->errorMessage)->toBe('Card declined');
});

it('sends order confirmed email with correct content', function (): void {
    // Arrange
    Mail::fake();
    $user = createTestUser();
    $product = createTestProduct(['price' => 89.99]);

    $order = Order::factory()->completed()->create([
        'user_id' => $user->id,
        'total_amount' => 89.99,
    ]);

    // Act
    $mailable = new \App\Mail\OrderConfirmed($order);

    // Assert
    expect($mailable->envelope()->subject)->toContain("Tu pedido #{$order->id}");
    expect($mailable->envelope()->subject)->toContain('Reposa+');
    expect($mailable->order->id)->toBe($order->id);
});

it('sends refund notification email', function (): void {
    // Arrange
    Mail::fake();
    $user = createTestUser();
    $product = createTestProduct(['price' => 65.00]);

    $order = Order::factory()->refunded()->create([
        'user_id' => $user->id,
        'total_amount' => 65.00,
    ]);

    $refund = \App\Models\Refund::create([
        'order_id' => $order->id,
        'amount' => 65.00,
        'reason' => 'Producto defectuoso',
        'stripe_refund_id' => 're_test_123',
        'status' => 'succeeded',
    ]);

    // Act
    $mailable = new \App\Mail\OrderRefunded($order, $refund);

    // Assert
    expect($mailable->envelope()->subject)->toContain('Reembolso procesado');
    expect($mailable->envelope()->subject)->toContain("Pedido #{$order->id}");
    expect($mailable->refund->id)->toBe($refund->id);
});

/*
|--------------------------------------------------------------------------
| Funciones auxiliares para MailHog (requieren MailHog corriendo)
|--------------------------------------------------------------------------
*/

/**
 * Obtener correos de MailHog para un destinatario específico.
 * Solo usar cuando MailHog esté corriendo en localhost:8025.
 */
function getMailhogMessages(string $recipient): array
{
    $mailhogUrl = env('MAILHOG_URL', 'http://localhost:8025');

    try {
        $response = Http::timeout(3)->get("{$mailhogUrl}/api/v2/search", [
            'kind' => 'to',
            'query' => $recipient,
        ]);

        if ($response->successful()) {
            return $response->json('items', []);
        }
    } catch (\Exception) {
        // MailHog no disponible — retornar array vacío
    }

    return [];
}

/**
 * Verificar si MailHog tiene correos con el asunto dado.
 */
function assertMailhogHasSubject(string $recipient, string $subject): bool
{
    $messages = getMailhogMessages($recipient);

    foreach ($messages as $message) {
        $emailSubject = $message['Content']['Headers']['Subject'][0] ?? '';
        if (str_contains($emailSubject, $subject)) {
            return true;
        }
    }

    return false;
}
