<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Mockery;
use Tests\TestCase;

class GoogleOAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_google_redirect_initiates_oauth_flow(): void
    {
        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn(new RedirectResponse('https://accounts.google.com/o/oauth2/auth'));

        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($provider);

        $response = $this->get('/auth/google');
        $response->assertRedirect('https://accounts.google.com/o/oauth2/auth');
    }

    public function test_login_and_register_screens_display_google_oauth_button(): void
    {
        $loginResponse = $this->get('/login');
        $loginResponse->assertStatus(200);
        $loginResponse->assertSee('Continuar con Google');
        $loginResponse->assertSee('/auth/google');

        $registerResponse = $this->get('/register');
        $registerResponse->assertStatus(200);
        $registerResponse->assertSee('Continuar con Google');
        $registerResponse->assertSee('/auth/google');
    }

    public function test_scenario_a_existing_user_with_google_id_logs_in_directly(): void
    {
        $user = User::factory()->create([
            'email' => 'sofia@example.com',
            'google_id' => 'google-uid-1001',
            'avatar' => 'https://example.com/old-avatar.jpg',
        ]);

        Address::create([
            'user_id' => $user->id,
            'street' => 'Paseo de Gracia 45',
            'city' => 'Barcelona',
            'zip_code' => '08007',
            'is_main' => true,
        ]);

        $googleUser = Mockery::mock(\Laravel\Socialite\Two\User::class);
        $googleUser->shouldReceive('getId')->andReturn('google-uid-1001');
        $googleUser->shouldReceive('getName')->andReturn('Sofía Navarro');
        $googleUser->shouldReceive('getEmail')->andReturn('sofia@example.com');
        $googleUser->shouldReceive('getAvatar')->andReturn('https://example.com/new-avatar.jpg');

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->once()->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('catalog'));
        $this->assertAuthenticatedAs($user);

        // Avatar updated
        $this->assertEquals('https://example.com/new-avatar.jpg', $user->fresh()->avatar);
    }

    public function test_scenario_b_existing_user_with_email_links_google_account(): void
    {
        $user = User::factory()->create([
            'email' => 'marcos@example.com',
            'google_id' => null,
            'avatar' => null,
        ]);

        Address::create([
            'user_id' => $user->id,
            'street' => 'Calle Mayor 10',
            'city' => 'Valencia',
            'zip_code' => '46001',
            'is_main' => true,
        ]);

        $googleUser = Mockery::mock(\Laravel\Socialite\Two\User::class);
        $googleUser->shouldReceive('getId')->andReturn('google-uid-2002');
        $googleUser->shouldReceive('getName')->andReturn('Marcos Ruiz');
        $googleUser->shouldReceive('getEmail')->andReturn('marcos@example.com');
        $googleUser->shouldReceive('getAvatar')->andReturn('https://example.com/marcos.jpg');

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->once()->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('catalog'));
        $this->assertAuthenticatedAs($user);

        $user->refresh();
        $this->assertEquals('google-uid-2002', $user->google_id);
        $this->assertEquals('https://example.com/marcos.jpg', $user->avatar);
    }

    public function test_scenario_c_new_user_via_google_is_created_and_redirected_to_onboarding(): void
    {
        $googleUser = Mockery::mock(\Laravel\Socialite\Two\User::class);
        $googleUser->shouldReceive('getId')->andReturn('google-uid-3003');
        $googleUser->shouldReceive('getName')->andReturn('Elena Vázquez');
        $googleUser->shouldReceive('getEmail')->andReturn('elena@example.com');
        $googleUser->shouldReceive('getAvatar')->andReturn('https://example.com/elena.jpg');

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->once()->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('onboarding.shipping'));
        $this->assertAuthenticated();

        $newUser = User::where('email', 'elena@example.com')->first();
        $this->assertNotNull($newUser);
        $this->assertEquals('google-uid-3003', $newUser->google_id);
        $this->assertEquals('Elena Vázquez', $newUser->name);
        $this->assertEquals('https://example.com/elena.jpg', $newUser->avatar);
        $this->assertNotNull($newUser->email_verified_at);
        $this->assertNull($newUser->password);
    }

    public function test_onboarding_middleware_intercepts_google_user_without_address(): void
    {
        $googleUser = User::factory()->create([
            'email' => 'incompleto@example.com',
            'google_id' => 'google-uid-4004',
        ]);

        // Attempt to browse protected pages or profile
        $response = $this->actingAs($googleUser)->get('/profile');
        $response->assertRedirect(route('onboarding.shipping'));

        // Attempt to browse catalog
        $responseCatalog = $this->actingAs($googleUser)->get('/catalog');
        $responseCatalog->assertRedirect(route('onboarding.shipping'));

        // Can access the onboarding form itself
        $responseOnboarding = $this->actingAs($googleUser)->get('/onboarding/shipping-address');
        $responseOnboarding->assertStatus(200);
        $responseOnboarding->assertSee('Dirección de Entrega');
    }

    public function test_google_user_can_complete_shipping_address_onboarding(): void
    {
        $googleUser = User::factory()->create([
            'name' => 'Adrián Morales',
            'email' => 'adrian@example.com',
            'google_id' => 'google-uid-5005',
        ]);

        $this->assertCount(0, $googleUser->addresses);

        $response = $this->actingAs($googleUser)->post('/onboarding/shipping-address', [
            'street' => 'Avenida Diagonal 220, 5º B',
            'city' => 'Barcelona',
            'zip_code' => '08018',
            'province' => 'Barcelona',
            'phone' => '+34 622 334 455',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('addresses', [
            'user_id' => $googleUser->id,
            'street' => 'Avenida Diagonal 220, 5º B',
            'city' => 'Barcelona',
            'zip_code' => '08018',
            'is_main' => true,
        ]);

        $this->assertDatabaseHas('profiles', [
            'user_id' => $googleUser->id,
            'phone' => '+34 622 334 455',
        ]);

        // Once onboarding is completed, user can freely access catalog and profile
        $catalogResponse = $this->actingAs($googleUser->fresh())->get('/catalog');
        $catalogResponse->assertStatus(200);
    }

    public function test_onboarding_validation_requires_mandatory_fields(): void
    {
        $googleUser = User::factory()->create([
            'email' => 'valida@example.com',
            'google_id' => 'google-uid-6006',
        ]);

        $response = $this->actingAs($googleUser)->post('/onboarding/shipping-address', []);
        $response->assertSessionHasErrors(['street', 'city', 'zip_code', 'phone']);
    }

    public function test_google_callback_handles_exceptions_gracefully(): void
    {
        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')
            ->once()
            ->andThrow(new \Exception('OAuth state mismatch'));

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_google_redirect_from_checkout_sets_intended_and_from_checkout(): void
    {
        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('redirect')
            ->once()
            ->andReturn(new RedirectResponse('https://accounts.google.com/o/oauth2/auth'));

        Socialite::shouldReceive('driver')
            ->with('google')
            ->andReturn($provider);

        $response = $this->get('/auth/google?redirect=checkout');
        $response->assertRedirect('https://accounts.google.com/o/oauth2/auth');
        $response->assertSessionHas('from_checkout', true);
        $response->assertSessionHas('url.intended', route('checkout.page'));
    }

    public function test_scenario_6_4_google_auth_from_checkout_merges_cart_and_redirects_to_checkout(): void
    {
        $product1 = Product::factory()->create(['price' => 45.00, 'stock' => 10]);
        $product2 = Product::factory()->create(['price' => 55.00, 'stock' => 5]);

        $user = User::factory()->create([
            'email' => 'carlos@example.com',
            'google_id' => 'google-uid-7007',
        ]);

        Address::create([
            'user_id' => $user->id,
            'street' => 'Gran Vía 12',
            'city' => 'Madrid',
            'zip_code' => '28013',
            'is_main' => true,
        ]);

        // Simular carrito de sesión previo de invitado con 2 productos y navegación por checkout
        session([
            'cart' => [
                $product1->id => ['quantity' => 1, 'price' => 45.00],
                $product2->id => ['quantity' => 2, 'price' => 55.00],
            ],
            'from_checkout' => true,
            'url.intended' => route('checkout.page'),
        ]);

        $googleUser = Mockery::mock(\Laravel\Socialite\Two\User::class);
        $googleUser->shouldReceive('getId')->andReturn('google-uid-7007');
        $googleUser->shouldReceive('getName')->andReturn('Carlos Almodóvar');
        $googleUser->shouldReceive('getEmail')->andReturn('carlos@example.com');
        $googleUser->shouldReceive('getAvatar')->andReturn('https://example.com/carlos.jpg');

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->once()->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');

        // Redirige al checkout, no al catálogo ni a la pantalla de inicio
        $response->assertRedirect(route('checkout.page'));
        $this->assertAuthenticatedAs($user);

        // Fusión de carrito verificada en base de datos mediante MergeCartOnLogin
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product1->id,
            'quantity' => 1,
        ]);
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product2->id,
            'quantity' => 2,
        ]);

        // El usuario accede al checkout y ve sus datos autenticados y dirección guardada
        $checkoutResponse = $this->actingAs($user)->get(route('checkout.page'));
        $checkoutResponse->assertStatus(200);
        $checkoutResponse->assertSee('Gran Vía 12');
        $checkoutResponse->assertSee('28013 Madrid');
    }

    public function test_google_auth_from_checkout_for_new_user_redirects_to_checkout_after_onboarding(): void
    {
        $product = Product::factory()->create(['price' => 50.00, 'stock' => 10]);

        session([
            'cart' => [
                $product->id => ['quantity' => 1, 'price' => 50.00],
            ],
            'from_checkout' => true,
            'url.intended' => route('checkout.page'),
        ]);

        $googleUser = Mockery::mock(\Laravel\Socialite\Two\User::class);
        $googleUser->shouldReceive('getId')->andReturn('google-uid-8008');
        $googleUser->shouldReceive('getName')->andReturn('Lucía Gómez');
        $googleUser->shouldReceive('getEmail')->andReturn('lucia@example.com');
        $googleUser->shouldReceive('getAvatar')->andReturn('https://example.com/lucia.jpg');

        $provider = Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->once()->andReturn($googleUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        // Al ser nuevo usuario sin dirección, callback lo manda a onboarding
        $response = $this->get('/auth/google/callback');
        $response->assertRedirect(route('onboarding.shipping'));

        $newUser = User::where('email', 'lucia@example.com')->first();
        $this->assertNotNull($newUser);

        // Al completar onboarding con carrito / desde checkout, debe redirigir a checkout
        $onboardingResponse = $this->actingAs($newUser)->post('/onboarding/shipping-address', [
            'street' => 'Calle Betis 15',
            'city' => 'Sevilla',
            'zip_code' => '41010',
            'province' => 'Sevilla',
            'phone' => '+34 655 443 322',
        ]);

        $onboardingResponse->assertRedirect(route('checkout.page'));
    }
}
