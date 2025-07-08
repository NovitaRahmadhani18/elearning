<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use LevelUp\Experience\Models\Level;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            /* ClassroomSeeder::class, */
        ]);

        Level::add(
            ['level' => 1, 'next_level_experience' => null],
            ['level' => 2, 'next_level_experience' => 100],
            ['level' => 3, 'next_level_experience' => 250],
            ['level' => 4, 'next_level_experience' => 250],
        );
    }
}
