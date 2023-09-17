<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Http\Resources\PokemonCollection;
use App\Http\Resources\PokemonResource;
use App\Models\Pokemon;

final  class PokemonsQuery
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        $query = $args["query"];

        $pokemonQuery = Pokemon::where('status', true);
        //確認關鍵字
        if (!empty($query)) {
            $pokemonQuery->where(function ($_query) use ($query) {
                $_query->where("name", "like", "%" . $query . "%")
                    ->orWhere('race', 'like', "%" . $query . '%')
                    ->orWhere('level', 'like', "%" . $query . '%');
            });
            $pokemon = $pokemonQuery->get();
            return $pokemon;
        } else {
            //無關鍵字回傳所有寶可夢
            $pokemon = Pokemon::where('status', true)->get();
            return $pokemon;
        }
    }
}
