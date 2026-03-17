<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ItemTypeSeeder::class,  // 1. Types d'abord
            TagSeeder::class,       // 2. Tags
            ProfileSeeder::class,   // 3. Profils
            ItemSeeder::class,      // 4. Items (dépend des types et tags)
        ]);
    }
}
