<?php
// database/seeders/ItemSeeder.php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\ItemType;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $livre    = ItemType::where('slug', 'livre')->first();
        $bd       = ItemType::where('slug', 'bd')->first();
        $jeuSoc   = ItemType::where('slug', 'jeu-societe')->first();
        $jeuVideo = ItemType::where('slug', 'jeu-video')->first();

        $fantasy   = Tag::where('slug', 'fantasy')->first();
        $jeunesse  = Tag::where('slug', 'jeunesse')->first();
        $aventure  = Tag::where('slug', 'aventure')->first();
        $famille   = Tag::where('slug', 'famille')->first();
        $cooperatif = Tag::where('slug', 'cooperatif')->first();
        $sf        = Tag::where('slug', 'science-fiction')->first();

        $items = [
            // Livres
            [
                'data' => [
                    'item_type_id'   => $livre?->id,
                    'title'          => 'Le Seigneur des Anneaux',
                    'author'         => 'J.R.R. Tolkien',
                    'publisher'      => 'Christian Bourgois',
                    'published_year' => 1954,
                    'language'       => 'fr',
                    'status'         => 'available',
                ],
                'tags' => [$fantasy?->id, $aventure?->id],
            ],
            [
                'data' => [
                    'item_type_id'   => $livre?->id,
                    'title'          => 'Harry Potter à l\'école des sorciers',
                    'author'         => 'J.K. Rowling',
                    'publisher'      => 'Gallimard Jeunesse',
                    'published_year' => 1997,
                    'language'       => 'fr',
                    'status'         => 'borrowed',
                ],
                'tags' => [$fantasy?->id, $jeunesse?->id],
            ],
            // BD
            [
                'data' => [
                    'item_type_id'   => $bd?->id,
                    'title'          => 'Astérix le Gaulois',
                    'author'         => 'René Goscinny',
                    'publisher'      => 'Dargaud',
                    'published_year' => 1961,
                    'language'       => 'fr',
                    'status'         => 'available',
                ],
                'tags' => [$aventure?->id, $jeunesse?->id],
            ],
            // Jeux de société
            [
                'data' => [
                    'item_type_id'   => $jeuSoc?->id,
                    'title'          => 'Pandemic',
                    'author'         => 'Matt Leacock',
                    'publisher'      => 'Z-Man Games',
                    'published_year' => 2008,
                    'language'       => 'fr',
                    'status'         => 'available',
                    'extra'          => ['nb_joueurs' => '2-4', 'duree' => '45 min'],
                ],
                'tags' => [$cooperatif?->id, $famille?->id],
            ],
            // Jeux vidéo
            [
                'data' => [
                    'item_type_id'   => $jeuVideo?->id,
                    'title'          => 'The Legend of Zelda: Breath of the Wild',
                    'author'         => 'Nintendo',
                    'publisher'      => 'Nintendo',
                    'published_year' => 2017,
                    'language'       => 'fr',
                    'status'         => 'available',
                    'extra'          => ['plateforme' => 'Nintendo Switch'],
                ],
                'tags' => [$aventure?->id, $fantasy?->id],
            ],
        ];

        foreach ($items as $entry) {
            $item = Item::firstOrCreate(
                ['title' => $entry['data']['title']],
                $entry['data']
            );

            // Attache les tags en filtrant les nulls
            $tagIds = array_filter($entry['tags']);
            if (! empty($tagIds)) {
                $item->tags()->syncWithoutDetaching($tagIds);
            }
        }
    }
}