<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Nature;

final readonly class NatureUpdate
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        $nature = $args["input"]["name"];
        $id = $args["input"]["id"];
        $newNature = Nature::find($id);
        $newNature->name = $nature;
        $newNature->save();
        return $newNature;
    }
}
