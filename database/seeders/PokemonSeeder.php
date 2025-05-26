<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pokemon;
use App\Services\PokemonBasicService;

class PokemonSeeder extends Seeder
{
    private $pokemonService;

    public function __construct(PokemonBasicService $pokemonService)
    {
        $this->pokemonService = $pokemonService;
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $url = $this->pokemonService->getPokemonPicture('gengar');
        for ($id = 1; $id < 99; $id++) {
            Pokemon::create([
                'name' => 'Gengar no.' . $id, 
                'level' => 50,
                'race' => 'gengar',
                'nature_id' => 1,
                'ability_id' => 1,
                'user_id' => 1,
                'image_url' => $url
            ]);
        }
    }
}
