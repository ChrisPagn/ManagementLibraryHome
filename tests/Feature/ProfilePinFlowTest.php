<?php

namespace Tests\Feature;

use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfilePinFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_protected_pages_redirect_without_profile_session(): void
    {
        $response = $this->get('/famille/home');
        $response->assertRedirect(route('famille.index'));
    }

    public function test_pin_verification_sets_active_profile_session(): void
    {
        $profile = Profile::factory()->create(['pin' => '1234']);

        $response = $this->post(route('famille.pin.verify', $profile), ['pin' => '1234']);

        $response->assertRedirect(route('famille.home'));
        $this->assertEquals($profile->id, session('active_profile_id'));
    }

    public function test_wrong_pin_returns_error(): void
    {
        $profile = Profile::factory()->create(['pin' => '1234']);

        $response = $this->from(route('famille.pin.show', $profile))
            ->post(route('famille.pin.verify', $profile), ['pin' => '0000']);

        $response->assertRedirect(route('famille.pin.show', $profile));
        $response->assertSessionHasErrors('pin');
    }
}
