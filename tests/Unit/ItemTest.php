<?php

namespace Tests\Unit;

use App\Models\Item;
use App\Models\ItemReview;
use App\Models\ItemType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_scope_available_returns_only_available_items(): void
    {
        Item::factory()->create(['status' => 'available']);
        Item::factory()->create(['status' => 'borrowed']);

        $available = Item::available()->get();

        $this->assertCount(1, $available);
        $this->assertTrue($available->first()->isAvailable());
    }

    public function test_scope_by_type_filters_by_slug(): void
    {
        $bookType = ItemType::factory()->create(['slug' => 'book']);
        $gameType = ItemType::factory()->create(['slug' => 'game']);

        Item::factory()->for($bookType, 'type')->create();
        Item::factory()->for($gameType, 'type')->create();

        $books = Item::byType('book')->get();

        $this->assertCount(1, $books);
        $this->assertEquals('book', $books->first()->type->slug);
    }

    public function test_average_rating_returns_float_or_null(): void
    {
        $item = Item::factory()->create();

        $this->assertNull($item->averageRating());

        ItemReview::factory()->for($item)->create(['rating' => 5]);
        ItemReview::factory()->for($item)->create(['rating' => 3]);

        $this->assertEquals(4.0, $item->averageRating());
    }
}
