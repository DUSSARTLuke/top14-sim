<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Season;

class SeasonSeeder extends Seeder
{
    public function run(): void
    {
        $seasons = [
            ['year' => '2019-2020', 'label' => 'Top 14 2019-2020'],
            ['year' => '2020-2021', 'label' => 'Top 14 2020-2021'],
            ['year' => '2021-2022', 'label' => 'Top 14 2021-2022'],
            ['year' => '2022-2023', 'label' => 'Top 14 2022-2023'],
            ['year' => '2023-2024', 'label' => 'Top 14 2023-2024'],
            ['year' => '2024-2025', 'label' => 'Top 14 2024-2025'],
        ];

        foreach ($seasons as $season) {
            Season::create($season);
        }
    }
}
