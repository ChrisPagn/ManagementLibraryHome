<?php

namespace Database\Factories;

use App\Models\ItemSuggestion;
use App\Models\ItemType;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemSuggestionFactory extends Factory
{
    protected $model = ItemSuggestion::class;

    public function definition(): array
    {
        return [
            'profile_id' => Profile::factory(),
            'item_type_id' => ItemType::factory(),
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->optional()->name(),
            'isbn' => $this->faker->optional()->isbn13(),
            'note' => $this->faker->optional()->sentence(),
            'status' => 'pending',
            'admin_note' => null,
        ];
    }

    public function approved(): self
    {
        return $this->state(fn () => ['status' => 'approved']);
    }
}
