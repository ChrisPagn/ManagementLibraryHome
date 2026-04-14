<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->firstName(),
            'avatar' => null,
            'pin' => '1234',
            'role' => 'member',
        ];
    }

    public function admin(): self
    {
        return $this->state(fn () => ['role' => 'admin']);
    }
}
