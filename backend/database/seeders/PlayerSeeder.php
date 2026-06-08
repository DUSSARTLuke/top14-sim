<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Player;
use App\Models\Team;
use App\Models\Season;
use App\Models\Position;

class PlayerSeeder extends Seeder
{
    public function run(): void
    {
        $seasons = Season::all();

        // Joueurs réels Top 14 avec leurs postes (number = numéro de poste)
        $playersData = [
            // Toulouse
            ['name' => 'Antoine Dupont',      'club' => 'Stade Toulousain', 'number' => 9,  'attack' => 95, 'defense' => 88, 'stamina' => 92, 'speed' => 95, 'rating' => 94, 'positions' => [9]],
            ['name' => 'Romain Ntamack',      'club' => 'Stade Toulousain', 'number' => 10, 'attack' => 90, 'defense' => 78, 'stamina' => 85, 'speed' => 82, 'rating' => 88, 'positions' => [10, 12]],
            ['name' => 'Cyril Baille',        'club' => 'Stade Toulousain', 'number' => 1,  'attack' => 78, 'defense' => 88, 'stamina' => 85, 'speed' => 65, 'rating' => 84, 'positions' => [1]],
            ['name' => 'Julien Marchand',     'club' => 'Stade Toulousain', 'number' => 2,  'attack' => 80, 'defense' => 85, 'stamina' => 82, 'speed' => 70, 'rating' => 83, 'positions' => [2]],
            ['name' => 'Emmanuel Meafou',     'club' => 'Stade Toulousain', 'number' => 4,  'attack' => 75, 'defense' => 88, 'stamina' => 83, 'speed' => 68, 'rating' => 82, 'positions' => [4, 5]],
            ['name' => 'Thibaud Flament',     'club' => 'Stade Toulousain', 'number' => 5,  'attack' => 74, 'defense' => 86, 'stamina' => 82, 'speed' => 67, 'rating' => 81, 'positions' => [4, 5]],
            ['name' => 'François Cros',       'club' => 'Stade Toulousain', 'number' => 7,  'attack' => 82, 'defense' => 87, 'stamina' => 85, 'speed' => 78, 'rating' => 84, 'positions' => [6, 7, 8]],
            ['name' => 'Cheslin Kolbe',       'club' => 'Stade Toulousain', 'number' => 14, 'attack' => 93, 'defense' => 80, 'stamina' => 86, 'speed' => 96, 'rating' => 91, 'positions' => [11, 14, 15]],
            ['name' => 'Thomas Ramos',        'club' => 'Stade Toulousain', 'number' => 15, 'attack' => 85, 'defense' => 80, 'stamina' => 84, 'speed' => 85, 'rating' => 85, 'positions' => [15, 10]],

            // La Rochelle
            ['name' => 'Grégory Alldritt',    'club' => 'Stade Rochelais',  'number' => 8,  'attack' => 90, 'defense' => 87, 'stamina' => 90, 'speed' => 80, 'rating' => 90, 'positions' => [8]],
            ['name' => 'Pierre Bourgarit',    'club' => 'Stade Rochelais',  'number' => 2,  'attack' => 78, 'defense' => 84, 'stamina' => 80, 'speed' => 72, 'rating' => 81, 'positions' => [2]],
            ['name' => 'Uini Atonio',         'club' => 'Stade Rochelais',  'number' => 3,  'attack' => 72, 'defense' => 90, 'stamina' => 82, 'speed' => 60, 'rating' => 82, 'positions' => [3]],
            ['name' => 'Will Skelton',        'club' => 'Stade Rochelais',  'number' => 5,  'attack' => 80, 'defense' => 89, 'stamina' => 84, 'speed' => 65, 'rating' => 85, 'positions' => [4, 5]],
            ['name' => 'Brice Dulin',         'club' => 'Stade Rochelais',  'number' => 15, 'attack' => 83, 'defense' => 78, 'stamina' => 82, 'speed' => 84, 'rating' => 82, 'positions' => [15, 14]],
            ['name' => 'Ihaia West',          'club' => 'Stade Rochelais',  'number' => 10, 'attack' => 85, 'defense' => 72, 'stamina' => 80, 'speed' => 78, 'rating' => 82, 'positions' => [10]],

            // Racing 92
            ['name' => 'Finn Russell',        'club' => 'Racing 92',        'number' => 10, 'attack' => 92, 'defense' => 70, 'stamina' => 80, 'speed' => 78, 'rating' => 88, 'positions' => [10]],
            ['name' => 'Nolann Le Garrec',    'club' => 'Racing 92',        'number' => 9,  'attack' => 82, 'defense' => 75, 'stamina' => 80, 'speed' => 83, 'rating' => 80, 'positions' => [9]],
            ['name' => 'Donovan Taofifenua',  'club' => 'Racing 92',        'number' => 4,  'attack' => 72, 'defense' => 85, 'stamina' => 80, 'speed' => 62, 'rating' => 79, 'positions' => [4, 5]],

            // Clermont
            ['name' => 'Irae Simone',         'club' => 'ASM Clermont',     'number' => 12, 'attack' => 84, 'defense' => 80, 'stamina' => 82, 'speed' => 78, 'rating' => 82, 'positions' => [12, 13]],
            ['name' => 'George Moala',        'club' => 'ASM Clermont',     'number' => 13, 'attack' => 83, 'defense' => 78, 'stamina' => 80, 'speed' => 82, 'rating' => 81, 'positions' => [13, 12]],
            ['name' => 'Damian Penaud',       'club' => 'ASM Clermont',     'number' => 11, 'attack' => 90, 'defense' => 74, 'stamina' => 84, 'speed' => 92, 'rating' => 88, 'positions' => [11, 14]],

            // Bordeaux
            ['name' => 'Matthieu Jalibert',   'club' => 'Union Bordeaux Bègles', 'number' => 10, 'attack' => 88, 'defense' => 72, 'stamina' => 82, 'speed' => 80, 'rating' => 86, 'positions' => [10, 9]],
            ['name' => 'Cameron Woki',        'club' => 'Union Bordeaux Bègles', 'number' => 8,  'attack' => 82, 'defense' => 84, 'stamina' => 83, 'speed' => 78, 'rating' => 83, 'positions' => [6, 7, 8]],
            ['name' => 'Maxime Lucu',         'club' => 'Union Bordeaux Bègles', 'number' => 9,  'attack' => 80, 'defense' => 74, 'stamina' => 78, 'speed' => 80, 'rating' => 79, 'positions' => [9]],

            // Toulon
            ['name' => 'Sergio Parisse',      'club' => 'RC Toulon',        'number' => 8,  'attack' => 84, 'defense' => 82, 'stamina' => 80, 'speed' => 72, 'rating' => 83, 'positions' => [8]],
            ['name' => 'Baptiste Serin',      'club' => 'RC Toulon',        'number' => 9,  'attack' => 80, 'defense' => 72, 'stamina' => 78, 'speed' => 78, 'rating' => 78, 'positions' => [9, 10]],

            // Montpellier
            ['name' => 'Paolo Garbisi',       'club' => 'Montpellier Hérault RC', 'number' => 10, 'attack' => 85, 'defense' => 70, 'stamina' => 78, 'speed' => 75, 'rating' => 81, 'positions' => [10]],
            ['name' => 'Cobus Reinach',       'club' => 'Montpellier Hérault RC', 'number' => 9,  'attack' => 82, 'defense' => 72, 'stamina' => 78, 'speed' => 85, 'rating' => 81, 'positions' => [9]],
        ];

        $positions = Position::all()->keyBy('number');

        foreach ($seasons as $season) {
            $realPlayerId = 1;

            foreach ($playersData as $index => $data) {
                $team = Team::where('name', $data['club'])
                    ->where('season_id', $season->id)
                    ->first();

                if (!$team) continue;

                $player = Player::create([
                    'real_player_id' => $index + 1,
                    'name'           => $data['name'],
                    'number'         => $data['number'],
                    'attack'         => $data['attack'],
                    'defense'        => $data['defense'],
                    'stamina'        => $data['stamina'],
                    'speed'          => $data['speed'],
                    'rating'         => $data['rating'],
                    'team_id'        => $team->id,
                    'season_id'      => $season->id,
                ]);

                // Associer les positions
                foreach ($data['positions'] as $i => $posNum) {
                    if (isset($positions[$posNum])) {
                        $player->positions()->attach($positions[$posNum]->id, [
                            'type' => $i === 0 ? 'primary' : 'secondary'
                        ]);
                    }
                }
            }
        }
    }
}
