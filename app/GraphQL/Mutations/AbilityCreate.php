<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Ability;

final readonly class AbilityCreate
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        $ability = $args["input"]["name"];
        $newAbility = Ability::create([
            "name" => $ability
        ]);
        return $newAbility;
    }
}
