<?php

namespace Tests\Feature;

use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_add_item_to_wishlist(): void
    {
        $profile = Profile::factory()->create(['pin' => '1234']);

        $this->withSession(['active_profile_id' => $profile->id]);

        $payload = [
            'title' => 'Dune',
            'author' => 'Frank Herbert',
            'priority' => 'high',
        ];

        $response = $this->post(route('famille.wishlist.store'), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('wishlists', [
            'profile_id' => $profile->id,
            'title' => 'Dune',
            'priority' => 'high',
        ]);
    }
}
