<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\User;
use App\Services\Shipping\ShippingServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private Product $product;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::create([
            'name' => 'Ergonómicas',
            'slug' => 'ergonomicas',
        ]);

        $this->product = Product::create([
            'name' => 'Almohada Cervical Viscoelástica Plus',
            'description' => 'Almohada ergonómica de descanso profundo.',
            'price' => 59.99,
            'stock' => 10,
            'category_id' => $this->category->id,
            'firmness' => 'Media-Alta',
            'material' => 'Viscoelástica',
        ]);
    }

    public function test_guest_can_view_adaptive_checkout_page_with_session_cart(): void
    {
        $response = $this->withSession([
            'cart' => [
                $this->product->id => ['quantity' => 2],
            ],
        ])->get('/checkout');

        $response->assertStatus(200);
        $response->assertViewIs('checkout.index');
        $response->assertSee('Almohada Cervical Viscoelástica Plus');
        $response->assertSee('Correos Express');
        $response->assertSee('Datos de Envío y Destinatario');
    }

    public function test_checkout_page_redirects_to_cart_if_cart_is_empty(): void
    {
        $response = $this->get('/checkout');

        $response->assertRedirect('/cart');
        $response->assertSessionHas('error');
    }

    public function test_guest_checkout_requires_mandatory_shipping_fields(): void
    {
        $response = $this->withSession([
            'cart' => [
                $this->product->id => ['quantity' => 1],
            ],
        ])->post('/checkout', []);

        $response->assertSessionHasErrors([
            'shipping_name',
            'shipping_email',
            'shipping_phone',
            'shipping_street',
            'shipping_city',
            'shipping_zip_code',
        ]);
    }

    public function test_guest_can_complete_order_with_token_and_shipment_generation(): void
    {
        $initialStock = $this->product->stock;

        $response = $this->withSession([
            'cart' => [
                $this->product->id => ['quantity' => 1],
            ],
        ])->post('/checkout', [
            'shipping_name' => 'Marta Domínguez',
            'shipping_email' => 'marta.dominguez@example.com',
            'shipping_phone' => '+34 600 112 233',
            'shipping_street' => 'Paseo del Prado 14, 2º B',
            'shipping_city' => 'Madrid',
            'shipping_zip_code' => '28014',
            'shipping_province' => 'Madrid',
            'shipping_service_type' => 'standard_48h',
        ]);

        $order = Order::where('shipping_email', 'marta.dominguez@example.com')->first();
        $this->assertNotNull($order);
        $this->assertNull($order->user_id);
        $this->assertTrue($order->isGuest());
        $this->assertNotNull($order->guest_token);
        $this->assertEquals('Marta Domínguez', $order->customer_name);
        $this->assertEquals('marta.dominguez@example.com', $order->customer_email);
        $this->assertEquals($initialStock - 1, $this->product->fresh()->stock);

        // Shipment created
        $shipment = Shipment::where('order_id', $order->id)->first();
        $this->assertNotNull($shipment);
        $this->assertStringStartsWith('RPX', $shipment->tracking_number);
        $this->assertEquals(Shipment::STATUS_PRE_REGISTERED, $shipment->status);

        // Redirected to show with guest token
        $response->assertRedirect(route('orders.show', ['order' => $order->id, 'token' => $order->guest_token]));
    }

    public function test_guest_can_view_order_and_download_invoice_with_valid_token(): void
    {
        $token = 'test-guest-token-1234567890abcdef';
        $order = Order::create([
            'user_id' => null,
            'guest_token' => $token,
            'shipping_name' => 'Lucía Ramos',
            'shipping_email' => 'lucia.ramos@example.com',
            'shipping_phone' => '654321987',
            'shipping_street' => 'Calle Mayor 1',
            'shipping_city' => 'Valencia',
            'shipping_zip_code' => '46001',
            'shipping_province' => 'Valencia',
            'shipping_country' => 'ES',
            'shipping_service_type' => 'standard_48h',
            'shipping_cost' => 0.00,
            'total_amount' => 59.99,
            'status' => 'pending',
        ]);

        $order->orderItems()->create([
            'product_id' => $this->product->id,
            'quantity' => 1,
            'price_at_purchase' => 59.99,
        ]);

        // Access with valid token in query param
        $response = $this->get('/orders/'.$order->id.'?token='.$token);
        $response->assertStatus(200);
        $response->assertSee('Lucía Ramos');
        $response->assertSee('Guarda tu cuenta en 1 clic');

        // Invoice download with valid token
        $invoiceResponse = $this->get('/orders/'.$order->id.'/invoice?token='.$token);
        $invoiceResponse->assertStatus(200);
        $this->assertEquals('application/pdf', $invoiceResponse->headers->get('content-type'));
    }

    public function test_guest_order_forbidden_without_valid_token(): void
    {
        $order = Order::create([
            'user_id' => null,
            'guest_token' => 'secure-token-999',
            'shipping_name' => 'Ana López',
            'shipping_email' => 'ana@example.com',
            'shipping_street' => 'Calle Real 5',
            'shipping_city' => 'Sevilla',
            'shipping_zip_code' => '41001',
            'shipping_country' => 'ES',
            'total_amount' => 59.99,
            'status' => 'pending',
        ]);

        // Access without token
        $response = $this->get('/orders/'.$order->id);
        $response->assertStatus(403);

        // Access with wrong token
        $responseWrong = $this->get('/orders/'.$order->id.'?token=wrong-token');
        $responseWrong->assertStatus(403);

        // Invoice without token
        $responseInvoice = $this->get('/orders/'.$order->id.'/invoice');
        $responseInvoice->assertStatus(403);
    }

    public function test_guest_can_claim_account_in_one_click(): void
    {
        $token = 'claim-token-12345';
        $order = Order::create([
            'user_id' => null,
            'guest_token' => $token,
            'shipping_name' => 'Javier Navarro',
            'shipping_email' => 'javier.navarro@example.com',
            'shipping_phone' => '+34 622 334 455',
            'shipping_street' => 'Calle Bailén 20, 3º D',
            'shipping_city' => 'Bilbao',
            'shipping_zip_code' => '48003',
            'shipping_province' => 'Vizcaya',
            'shipping_country' => 'ES',
            'total_amount' => 59.99,
            'status' => 'pending',
        ]);

        $response = $this->post('/orders/'.$order->id.'/claim-account', [
            'token' => $token,
            'password' => 'SecurePass123!',
            'password_confirmation' => 'SecurePass123!',
        ]);

        $response->assertRedirect(route('profile'));

        $user = User::where('email', 'javier.navarro@example.com')->first();
        $this->assertNotNull($user);
        $this->assertAuthenticatedAs($user);

        // Order linked to user
        $this->assertEquals($user->id, $order->fresh()->user_id);

        // User address created
        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'street' => 'Calle Bailén 20, 3º D',
            'city' => 'Bilbao',
            'zip_code' => '48003',
            'is_main' => true,
        ]);

        // User profile phone saved
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'phone' => '+34 622 334 455',
        ]);
    }

    public function test_admin_can_advance_shipment_and_view_label(): void
    {
        $admin = User::create([
            'name' => 'Admin Reposa',
            'email' => 'admin@reposaplus.test',
            'password' => bcrypt('AdminPass123!'),
            'role' => 'admin',
        ]);

        $order = Order::create([
            'user_id' => null,
            'guest_token' => 'admin-test-token',
            'shipping_name' => 'Cliente Envío',
            'shipping_email' => 'cliente@test.es',
            'shipping_phone' => '600000000',
            'shipping_street' => 'Calle Alcalá 1',
            'shipping_city' => 'Madrid',
            'shipping_zip_code' => '28014',
            'shipping_country' => 'ES',
            'total_amount' => 59.99,
            'status' => 'pending',
        ]);

        $shippingService = app(ShippingServiceInterface::class);
        $shipment = $shippingService->createShipment($order, [
            'name' => 'Cliente Envío',
            'email' => 'cliente@test.es',
            'street' => 'Calle Alcalá 1',
            'city' => 'Madrid',
            'zip_code' => '28014',
        ]);

        $this->assertEquals(Shipment::STATUS_PRE_REGISTERED, $shipment->status);

        // Advance shipment
        $response = $this->actingAs($admin)->post('/admin/shipments/'.$shipment->id.'/advance');
        $response->assertRedirect();
        $this->assertEquals(Shipment::STATUS_IN_TRANSIT, $shipment->fresh()->status);

        // View thermal label
        $labelResponse = $this->actingAs($admin)->get('/admin/shipments/'.$shipment->id.'/label');
        $labelResponse->assertStatus(200);
        $labelResponse->assertViewIs('admin.shipments.label');
        $labelResponse->assertSee($shipment->tracking_number);
    }
}
