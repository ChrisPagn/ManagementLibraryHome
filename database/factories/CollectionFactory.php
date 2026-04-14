<?php

namespace Database\Factories;

use App\Models\Collection;
use App\Models\ItemType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CollectionFactory extends Factory
{
    protected $model = Collection::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);

        return [
            'item_type_id' => ItemType::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => $this->faker->optional()->sentence(),
            'author' => $this->faker->optional()->name(),
            'total_volumes' => $this->faker->numberBetween(3, 20),
            'is_complete' => $this->faker->boolean(),
            'cover' => null,
        ];
    }
}
