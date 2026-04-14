<?php

namespace Tests\Feature;

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportJsonTest extends TestCase
{
    use RefreshDatabase;

    public function test_mediatheque_json_exports_items(): void
    {
        Item::factory()->count(2)->create();

        $response = $this->get('/export/mediatheque/json');

        $response->assertOk();
        $response->assertHeader('Content-Disposition');
        $data = $response->json();

        $this->assertEquals(2, $data['stats']['total_items']);
        $this->assertCount(2, $data['items']);
    }
}
