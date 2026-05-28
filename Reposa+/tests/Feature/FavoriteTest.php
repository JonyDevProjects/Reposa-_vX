<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_toggle_favorite()
    {
        $product = Product::factory()->create();

        $response = $this->postJson("/favorites/toggle/{$product->id}");

        $response->assertStatus(401)
                 ->assertJson([
                     'success' => false,
                     'redirect' => route('login')
                 ]);
    }

    public function test_user_can_toggle_favorite()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)
                         ->postJson("/favorites/toggle/{$product->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'is_favorite' => true
                 ]);

        $this->assertTrue($user->favorites->contains($product->id));

        // Toggle again to remove
        $response = $this->actingAs($user)
                         ->postJson("/favorites/toggle/{$product->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'is_favorite' => false
                 ]);

        $this->assertFalse($user->fresh()->favorites->contains($product->id));
    }
}
