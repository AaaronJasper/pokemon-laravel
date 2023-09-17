<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Ability;
use App\Models\Nature;
use App\Models\Pokemon;
use App\Services\PokemonService;

final  class PokemonCreate
{
    private PokemonService $pokemonService;

    public function __construct(PokemonService $pokemonService)
    {
        $this->pokemonService = $pokemonService;
    }
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        //先確認種族
        $race = $args["input"]["race"];
        $data = $this->pokemonService->checkPokemonRace($race);
        if (empty($data)) {
            return null;
        }
        //取得id
        $nature = Nature::where("name", $args["input"]["nature"])->value("id");
        $ability = Ability::where("name", $args["input"]["ability"])->value("id");
        //確認是否進化
        $level = $args["input"]["level"];
        $race = $this->pokemonService->checkPokemonEvolution($race, $level);
        //建立寶可夢
        $pokemon = Pokemon::create([
            "name" => $args['input']["name"],
            "level" => $level,
            "race" => $race,
            "nature_id" => $nature,
            "ability_id" => $ability
        ]);
        return $pokemon;
    }
}
