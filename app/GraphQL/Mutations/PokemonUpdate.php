<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Ability;
use App\Models\Nature;
use App\Models\Pokemon;
use App\Services\PokemonService;

final  class PokemonUpdate
{
    private PokemonService $pokemonService;

    public function __construct(PokemonService $pokemonService)
    {
        $this->pokemonService = $pokemonService;
    }
    /** @param  array{}  $args */
    public function __invoke($_, array $args)
    {
        $id = $args["input"]["id"];
        //取得寶可夢
        $pokemon = Pokemon::find($id);
        if ($pokemon->status == false) {
            return null;
        }
        //逐一確認更新資料
        if (isset($args["input"]["name"]) && !empty($args["input"]["name"])) {
            $name = $args["input"]["name"];
            $pokemon->name = $name;
        }
        //等級是否大於或小於
        if (isset($args["input"]["level"]) && !empty($args["input"]["level"]) && $args["input"]["level"] <= $pokemon->level) {
            $level = $args["input"]["level"];
            $pokemon->level = $level;
        }
        if (isset($args["input"]["level"]) && !empty($args["input"]["level"]) && $args["input"]["level"] > $pokemon->level) {
            $level = $args["input"]["level"];
            $pokemon->level = $level;
            $pokemon->race = $this->pokemonService->checkPokemonEvolution($pokemon->race, $level);
        }
        //確認性格
        if (isset($args["input"]["nature"]) && !empty($args["input"]["nature"])) {
            $nature = $args["input"]["nature"];
            $pokemon->nature_id = Nature::where("name", $nature)->value("id");
        }
        //確認特性
        if (isset($args["input"]["ability"]) && !empty($args["input"]["ability"])) {
            $ability = $args["input"]["ability"];
            $pokemon->ability_id = Ability::where("name", $ability)->value("id");
        }
        $pokemon->save();
        return $pokemon;
    }
}
