<?php
// database/seeders/TagSeeder.php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'Fantasy',       'color' => '#8B5CF6'],
            ['name' => 'Science-fiction','color' => '#3B82F6'],
            ['name' => 'Thriller',      'color' => '#EF4444'],
            ['name' => 'Roman',         'color' => '#F59E0B'],
            ['name' => 'Histoire',      'color' => '#10B981'],
            ['name' => 'Jeunesse',      'color' => '#EC4899'],
            ['name' => 'Humour',        'color' => '#F97316'],
            ['name' => 'Aventure',      'color' => '#06B6D4'],
            ['name' => 'Famille',       'color' => '#84CC16'],
            ['name' => 'Coopératif',    'color' => '#14B8A6'],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(
                ['slug' => Str::slug($tag['name'])],
                $tag
            );
        }
    }
}