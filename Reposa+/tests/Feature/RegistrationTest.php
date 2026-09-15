<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Dirección de Entrega');
    }

    public function test_registration_requires_shipping_address_and_phone(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms' => true,
        ]);

        $response->assertSessionHasErrors(['street', 'city', 'zip_code', 'phone']);
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    }

    public function test_user_can_register_with_mandatory_shipping_address(): void
    {
        $response = $this->post('/register', [
            'name' => 'Carlos Benítez',
            'email' => 'carlos.benitez@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'street' => 'Calle Gran Vía 32, 4º A',
            'city' => 'Madrid',
            'zip_code' => '28013',
            'province' => 'Madrid',
            'phone' => '+34 611 223 344',
            'terms' => true,
        ]);

        $response->assertRedirect();
        $this->assertAuthenticated();

        $user = User::where('email', 'carlos.benitez@example.com')->first();
        $this->assertNotNull($user);

        // Address created and marked as main
        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'street' => 'Calle Gran Vía 32, 4º A',
            'city' => 'Madrid',
            'zip_code' => '28013',
            'is_main' => true,
        ]);

        // Profile created with phone
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'phone' => '+34 611 223 344',
        ]);
    }
}
