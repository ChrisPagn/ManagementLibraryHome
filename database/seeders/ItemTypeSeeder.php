<?php
// database/seeders/ItemTypeSeeder.php

namespace Database\Seeders;

use App\Models\ItemType;
use Illuminate\Database\Seeder;

class ItemTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name' => 'Livre',
                'slug' => 'livre',
                'icon' => 'heroicon-o-book-open',
            ],
            [
                'name' => 'Bande dessinée',
                'slug' => 'bd',
                'icon' => 'heroicon-o-book-open',
            ],
            [
                'name' => 'Jeu de société',
                'slug' => 'jeu-societe',
                'icon' => 'heroicon-o-puzzle-piece',
            ],
            [
                'name' => 'Jeu vidéo',
                'slug' => 'jeu-video',
                'icon' => 'heroicon-o-computer-desktop',
            ],
        ];

        foreach ($types as $type) {
            ItemType::firstOrCreate(
                ['slug' => $type['slug']],
                $type
            );
        }
    }
}