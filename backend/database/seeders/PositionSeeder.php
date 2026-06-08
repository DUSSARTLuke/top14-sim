<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;

class PositionSeeder extends Seeder
{
    public function run(): void
    {
        $positions = [
            ['number' => 1,  'name' => 'Pilier gauche',     'code' => 'PG', 'zone' => 'forward', 'weight_attack' => 15, 'weight_defense' => 35, 'weight_stamina' => 35, 'weight_speed' => 15],
            ['number' => 2,  'name' => 'Talonneur',         'code' => 'T',  'zone' => 'forward', 'weight_attack' => 20, 'weight_defense' => 35, 'weight_stamina' => 30, 'weight_speed' => 15],
            ['number' => 3,  'name' => 'Pilier droit',      'code' => 'PD', 'zone' => 'forward', 'weight_attack' => 15, 'weight_defense' => 35, 'weight_stamina' => 35, 'weight_speed' => 15],
            ['number' => 4,  'name' => '2ème ligne',        'code' => 'LL', 'zone' => 'forward', 'weight_attack' => 20, 'weight_defense' => 35, 'weight_stamina' => 30, 'weight_speed' => 15],
            ['number' => 5,  'name' => '2ème ligne',        'code' => 'LL', 'zone' => 'forward', 'weight_attack' => 20, 'weight_defense' => 35, 'weight_stamina' => 30, 'weight_speed' => 15],
            ['number' => 6,  'name' => '3ème ligne aile',   'code' => 'FL', 'zone' => 'forward', 'weight_attack' => 25, 'weight_defense' => 35, 'weight_stamina' => 25, 'weight_speed' => 15],
            ['number' => 7,  'name' => '3ème ligne aile',   'code' => 'FL', 'zone' => 'forward', 'weight_attack' => 25, 'weight_defense' => 35, 'weight_stamina' => 25, 'weight_speed' => 15],
            ['number' => 8,  'name' => '3ème ligne centre', 'code' => 'N8', 'zone' => 'forward', 'weight_attack' => 30, 'weight_defense' => 30, 'weight_stamina' => 25, 'weight_speed' => 15],
            ['number' => 9,  'name' => 'Demi de mêlée',    'code' => 'DM', 'zone' => 'back',    'weight_attack' => 35, 'weight_defense' => 20, 'weight_stamina' => 25, 'weight_speed' => 20],
            ['number' => 10, 'name' => 'Demi d\'ouverture', 'code' => 'DO', 'zone' => 'back',    'weight_attack' => 40, 'weight_defense' => 20, 'weight_stamina' => 20, 'weight_speed' => 20],
            ['number' => 11, 'name' => 'Ailier gauche',     'code' => 'AG', 'zone' => 'back',    'weight_attack' => 30, 'weight_defense' => 20, 'weight_stamina' => 20, 'weight_speed' => 30],
            ['number' => 12, 'name' => 'Centre',            'code' => 'CE', 'zone' => 'back',    'weight_attack' => 35, 'weight_defense' => 25, 'weight_stamina' => 20, 'weight_speed' => 20],
            ['number' => 13, 'name' => 'Centre',            'code' => 'CE', 'zone' => 'back',    'weight_attack' => 35, 'weight_defense' => 25, 'weight_stamina' => 20, 'weight_speed' => 20],
            ['number' => 14, 'name' => 'Ailier droit',      'code' => 'AD', 'zone' => 'back',    'weight_attack' => 30, 'weight_defense' => 20, 'weight_stamina' => 20, 'weight_speed' => 30],
            ['number' => 15, 'name' => 'Arrière',           'code' => 'AR', 'zone' => 'back',    'weight_attack' => 30, 'weight_defense' => 25, 'weight_stamina' => 20, 'weight_speed' => 25],
        ];

        foreach ($positions as $position) {
            Position::create($position);
        }
    }
}
