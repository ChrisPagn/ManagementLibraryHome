<?php

namespace Tests\Unit;

use App\Models\Item;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_create_tag_with_factory(): void
    {
        $tag = Tag::factory()->create(['name' => 'Science-Fiction']);

        $this->assertDatabaseHas('tags', ['name' => 'Science-Fiction']);
    }

    /** @test */
    public function belongs_to_many_items_relationship(): void
    {
        $tag = Tag::factory()->create();
        $items = Item::factory()->count(3)->create();
        $tag->items()->attach($items);

        $this->assertCount(3, $tag->items);
    }

    /** @test */
    public function scope_popular_returns_most_used_tags(): void
    {
        $popularTag = Tag::factory()->create();
        $rareTag = Tag::factory()->create();
        
        $item1 = Item::factory()->create();
        $item2 = Item::factory()->create();
        $popularTag->items()->attach([$item1->id, $item2->id]);
        $rareTag->items()->attach($item1->id);

$popularTags = Tag::withCount('items')->orderBy('items_count', 'desc')->first();

$this->assertEquals(2, $popularTags->items_count);
    }

    /** @test */
    public function can_attach_detach_items(): void
    {
        $tag = Tag::factory()->create();
        $item = Item::factory()->create();

        $tag->items()->attach($item);
        $this->assertCount(1, $tag->fresh()->items);

        $tag->items()->detach($item);
        $this->assertCount(0, $tag->fresh()->items);
    }
}
