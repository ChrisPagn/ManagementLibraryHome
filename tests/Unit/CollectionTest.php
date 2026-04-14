<?php

namespace Tests\Unit;

use App\Models\Collection;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_owned_and_missing_volume_numbers_and_completion(): void
    {
        $collection = Collection::factory()->create(['total_volumes' => 5]);
        $items = Item::factory()->count(2)->create();

        $collection->items()->attach($items[0]->id, ['volume_number' => 1]);
        $collection->items()->attach($items[1]->id, ['volume_number' => 3]);

        $this->assertSame([1, 3], $collection->fresh()->ownedVolumeNumbers());
        $this->assertSame([2, 4, 5], $collection->fresh()->missingVolumeNumbers());
        $this->assertEquals(40, $collection->fresh()->completionPercentage());
    }
}
