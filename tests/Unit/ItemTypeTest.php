<?php

namespace Tests\Unit;

use App\Models\Item;
use App\Models\ItemType;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemTypeTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_create_item_type_with_factory(): void
    {
        $itemType = ItemType::factory()->create([
            'name' => 'Roman',
            'slug' => 'roman',
        ]);

        $this->assertDatabaseHas('item_types', [
            'name' => 'Roman',
            'slug' => 'roman',
        ]);
        $this->assertEquals('roman', $itemType->slug);
    }

    /** @test */
    public function has_many_items_relationship(): void
    {
        $itemType = ItemType::factory()->create();
Item::factory()->count(3)->for($itemType, 'type')->create();

        $this->assertCount(3, $itemType->items);
    }

/** @test */
    public function can_retrieve_items_count(): void
    {
        $itemType = ItemType::factory()->create();
        Item::factory()->count(3)->for($itemType, 'type')->create();

        $this->assertEquals(3, $itemType->fresh()->items_count);
    }

    /** @test */
    public function items_count_accessor_works(): void
    {
        $itemType = ItemType::factory()->create();
Item::factory()->count(5)->for($itemType, 'type')->create();

        $this->assertEquals(5, $itemType->fresh()->items_count);
    }
}
