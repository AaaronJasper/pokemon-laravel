<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Pokemon;
use Illuminate\Support\Facades\Http;

final readonly class EnableSkills
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        $id=$args['id'];
        $pokemon = Pokemon::find($id);
        $pokemonRace = $pokemon->race;
        //取得可學習技能
        $url = 'https://pokeapi.co/api/v2/pokemon/' . $pokemonRace;
        $response = Http::get($url);
        $data = $response->json();
        //取得可學習技能
        $allSkills = [];
        foreach ($data['moves'] as $i => $move) {
            $key = 'skill' . ($i + 1);
            $allSkills[$key] = $move['move']['name'];
        }
        //回傳技能鍵與值
        return array_map(function ($skill) {
            return ['name' => $skill];
        }, $allSkills);
    }
}
