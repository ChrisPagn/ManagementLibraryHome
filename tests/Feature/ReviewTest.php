<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_create_or_update_review(): void
    {
        $profile = Profile::factory()->create(['pin' => '1234']);
        $item = Item::factory()->create();

        $this->withSession(['active_profile_id' => $profile->id]);

        $payload = [
            'reading_status' => 'completed',
            'rating' => 5,
            'comment' => 'Excellent',
        ];

        $response = $this->post(route('famille.review.store', $item), $payload);

        $response->assertRedirect();
        $this->assertDatabaseHas('item_reviews', [
            'item_id' => $item->id,
            'profile_id' => $profile->id,
            'rating' => 5,
        ]);

        // Update same review
        $payload['rating'] = 4;
        $this->post(route('famille.review.store', $item), $payload);

        $this->assertDatabaseHas('item_reviews', [
            'item_id' => $item->id,
            'profile_id' => $profile->id,
            'rating' => 4,
        ]);
    }
}
