<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_create_user(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
        $this->assertTrue(Hash::check('password', $user->password));
    }

    /** @test */
    public function can_check_admin_status(): void
    {
        $admin = User::factory()->create([
            'is_admin' => false,
        ]);

        $admin->update(['is_admin' => true]);

        $this->assertTrue($admin->fresh()->isAdmin());

        $user = User::factory()->create();
        $this->assertFalse($user->isAdmin());
    }
}
