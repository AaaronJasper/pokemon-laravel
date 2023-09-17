<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Pokemon;

final  class PokemonDelete
{
    /** @param  array{}  $args */
    public function __invoke($_, array $args)
    {
        $id = $args["input"]["id"];
        //查詢id
        $pokemon = Pokemon::find($id);
        if ($pokemon == null) {
            return null;
        }
        //確認是否已刪除過
        if ($pokemon->status == false) {
            return null;
        }
        //返回資料格式
        $pokemon->status = false;
        $pokemon->save();
        return $pokemon;
    }
}
