<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Nature;

final  class NatureCreate
{
    /** @param  array{}  $args */
    public function __invoke($_, array $args)
    {
        $name = $args["input"]["name"];
        $newNature = Nature::create([
            "name" => $name
        ]);
        return $newNature;
    }
}
