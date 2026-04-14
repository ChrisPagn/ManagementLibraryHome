<?php

namespace Tests\Feature;

use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuggestionTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_submit_suggestion(): void
    {
        $profile = Profile::factory()->create(['pin' => '1234']);
        $this->withSession(['active_profile_id' => $profile->id]);

        $payload = [
            'title' => 'Nouveau livre',
            'author' => 'Auteur',
        ];

        $response = $this->post(route('famille.suggestion.store'), $payload);

        $response->assertRedirect(route('famille.home'));
        $this->assertDatabaseHas('item_suggestions', [
            'profile_id' => $profile->id,
            'title' => 'Nouveau livre',
            'status' => 'pending',
        ]);
    }
}
