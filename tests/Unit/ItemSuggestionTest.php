<?php

namespace Tests\Unit;

use App\Models\Item;
use App\Models\ItemSuggestion;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemSuggestionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_create_suggestion(): void
    {
        $suggestion = ItemSuggestion::factory()->create([
            'title' => 'Nouveau livre suggéré',
            'author' => 'Auteur inconnu',
        ]);

        $this->assertDatabaseHas('item_suggestions', [
            'title' => 'Nouveau livre suggéré',
        ]);
    }

    /** @test */
    public function belongs_to_user(): void
    {
        $user = User::factory()->create();
        $suggestion = ItemSuggestion::factory()->for($user)->create();

        $this->assertEquals($user->id, $suggestion->user_id);
    }

    /** @test */
    public function belongs_to_profile_optional(): void
    {
        $profile = Profile::factory()->create();
        $suggestion = ItemSuggestion::factory()->for($profile)->create();

        $this->assertEquals($profile->id, $suggestion->profile_id);
    }

    /** @test */
    public function status_is_pending_by_default(): void
    {
        $suggestion = ItemSuggestion::factory()->create();

        $this->assertEquals('pending', $suggestion->status);
    }

    /** @test */
    public function scope_pending(): void
    {
        ItemSuggestion::factory()->create(['status' => 'pending']);
        ItemSuggestion::factory()->create(['status' => 'approved']);

        $pending = ItemSuggestion::pending()->count();

        $this->assertEquals(1, $pending);
    }
}
