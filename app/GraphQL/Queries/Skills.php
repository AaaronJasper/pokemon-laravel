<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Pokemon;

final readonly class Skills
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        $id = $args['id'];
        $pokemon = Pokemon::find($id);
        $pokemonSkills = [];
        $pokemonSkills[] = ['name' => $pokemon->skill1];
        $pokemonSkills[] = ['name' => $pokemon->skill2];
        $pokemonSkills[] = ['name' => $pokemon->skill3];
        $pokemonSkills[] = ['name' => $pokemon->skill4];
        return $pokemonSkills;
    }
}
