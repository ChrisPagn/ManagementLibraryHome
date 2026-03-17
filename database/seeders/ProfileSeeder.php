<?php
// database/seeders/ProfileSeeder.php

namespace Database\Seeders;

use App\Models\Profile;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    public function run(): void
    {
        $profiles = [
            [
                'name'   => 'Christopher',
                'role'   => 'admin',
                'pin'    => '1234',
                'avatar' => null,
            ],
            [
                'name'   => 'Evodie',
                'role'   => 'member',
                'pin'    => '0000',
                'avatar' => null,
            ],
            [
                'name'   => 'Dany',
                'role'   => 'member',
                'pin'    => '1111',
                'avatar' => null,
            ],
            [
                'name'   => 'Hanae',
                'role'   => 'member',
                'pin'    => '2222',
                'avatar' => null,
            ],
        ];

        foreach ($profiles as $profile) {
            Profile::firstOrCreate(
                ['name' => $profile['name']],
                $profile
            );
        }
    }
}