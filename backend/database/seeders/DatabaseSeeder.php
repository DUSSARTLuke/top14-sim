<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SeasonSeeder::class,
            TeamSeeder::class,
            PositionSeeder::class,
            PlayerSeeder::class,
        ]);
    }
}
