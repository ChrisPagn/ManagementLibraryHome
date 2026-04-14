<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\ItemType;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'item_type_id' => ItemType::factory(),
            'title' => $this->faker->sentence(3),
            'subtitle' => $this->faker->optional()->sentence(4),
            'description' => $this->faker->optional()->paragraph(),
            'author' => $this->faker->name(),
            'publisher' => $this->faker->company(),
            'published_year' => $this->faker->optional()->year(),
            'language' => 'fr',
            'isbn' => $this->faker->unique()->isbn13(),
            'extra' => ['pages' => $this->faker->numberBetween(100, 500)],
            'status' => 'available',
            'owner_profile_id' => Profile::factory(),
        ];
    }

    public function borrowed(): self
    {
        return $this->state(fn () => ['status' => 'borrowed']);
    }
}
