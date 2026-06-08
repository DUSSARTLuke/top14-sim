<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\Season;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $clubs = [
            ['name' => 'Stade Toulousain',        'city' => 'Toulouse'],
            ['name' => 'Racing 92',               'city' => 'Paris'],
            ['name' => 'Stade Rochelais',         'city' => 'La Rochelle'],
            ['name' => 'Castres Olympique',       'city' => 'Castres'],
            ['name' => 'Montpellier Hérault RC',  'city' => 'Montpellier'],
            ['name' => 'Stade Français Paris',    'city' => 'Paris'],
            ['name' => 'Association Sportive Montferrand Clermont',            'city' => 'Clermont-Ferrand'],
            ['name' => 'Union Bordeaux Bègles',   'city' => 'Bordeaux'],
            ['name' => 'Lyon Olympique Universitaire', 'city' => 'Lyon'],
            ['name' => 'Rugby Club Toulon',       'city' => 'Toulon'],
            ['name' => 'Union Sportive Association Perpignan', 'city' => 'Perpignan'],
            ['name' => 'Aviron Bayonnais',        'city' => 'Bayonne'],
            ['name' => 'Brive',                   'city' => 'Brive'],
            ['name' => 'Section Paloise',         'city' => 'Pau'],
        ];

        $seasons = Season::all();

        foreach ($seasons as $season) {
            foreach ($clubs as $club) {
                Team::create([
                    'name'      => $club['name'],
                    'city'      => $club['city'],
                    'season_id' => $season->id,
                ]);
            }
        }
    }
}
