<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pokemon;

class PokemonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($id = 1; $id < 99; $id++) {
            Pokemon::create([
                'name' => 'Gengar no.' . $id, 
                'level' => 50,
                'race' => 'gengar',
                'nature_id' => 1,
                'ability_id' => 1,
                'user_id' => 1
            ]);
        }
    }
}
