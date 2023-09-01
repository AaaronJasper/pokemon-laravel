<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Ability;

final readonly class AbilityUpdate
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        $ability = $args["input"]["name"];
        $id = $args["input"]["id"];
        $newAbility = Ability::find($id);
        $newAbility->name = $ability;
        $newAbility->save();
        return $newAbility;
    }
}
