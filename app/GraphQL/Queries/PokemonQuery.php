<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Pokemon;

final  class PokemonQuery
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        $id=$args["id"];
        $pokemon = Pokemon::find($id);
        if($pokemon == null){
            return null;
        }
        if($pokemon->status == false){
            return null;
        }
        return $pokemon;
    }
}
