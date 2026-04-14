<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\ItemReview;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemReviewFactory extends Factory
{
    protected $model = ItemReview::class;

    public function definition(): array
    {
        return [
            'item_id' => Item::factory(),
            'profile_id' => Profile::factory(),
            'reading_status' => $this->faker->randomElement(['to_read', 'in_progress', 'completed']),
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->optional()->sentence(),
        ];
    }
}
