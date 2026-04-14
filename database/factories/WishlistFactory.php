<?php

namespace Database\Factories;

use App\Models\ItemType;
use App\Models\Profile;
use App\Models\Wishlist;
use Illuminate\Database\Eloquent\Factories\Factory;

class WishlistFactory extends Factory
{
    protected $model = Wishlist::class;

    public function definition(): array
    {
        return [
            'profile_id' => Profile::factory(),
            'item_type_id' => ItemType::factory(),
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->optional()->name(),
            'isbn' => $this->faker->optional()->isbn13(),
            'note' => $this->faker->optional()->sentence(),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'estimated_price' => $this->faker->optional()->randomFloat(2, 5, 60),
            'is_acquired' => false,
            'acquired_at' => null,
        ];
    }

    public function acquired(): self
    {
        return $this->state(fn () => [
            'is_acquired' => true,
            'acquired_at' => now(),
        ]);
    }
}
