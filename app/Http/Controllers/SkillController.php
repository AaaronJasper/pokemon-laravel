<?php

namespace App\Http\Controllers;

use App\Http\Resources\PokemonResource;
use App\Models\Pokemon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SkillController extends BaseController
{
    //顯示可學習技能
    public function index(string $id)
    {
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
        return $allSkills;
    }

    //顯示已學習技能
    public function show(string $id)
    {
        $pokemon = Pokemon::find($id);
        $pokemonSkill = [];
        $pokemonSkill['skill1'] = $pokemon->skill1;
        $pokemonSkill['skill2'] = $pokemon->skill2;
        $pokemonSkill['skill3'] = $pokemon->skill3;
        $pokemonSkill['skill4'] = $pokemon->skill4;
        return $this->res(200, $pokemonSkill, "Search successful");
    }

    //學習技能
    public function learn(Request $request, string $id)
    {
        $pokemon = Pokemon::find($id);
        $skill1 = $request->skill1;
        $skill2 = $request->skill2;
        $skill3 = $request->skill3;
        $skill4 = $request->skill4;
        //取得可學習技能
        $enableSkill = $this->index($id);
        //更新技能
        if (!empty($skill1) && in_array($skill1, $enableSkill)) {
            $pokemon->skill1 = $skill1;
        }
        if (!empty($skill2) && in_array($skill2, $enableSkill)) {
            $pokemon->skill2 = $skill2;
        }
        if (!empty($skill3) && in_array($skill3, $enableSkill)) {
            $pokemon->skill3 = $skill3;
        }
        if (!empty($skill4) && in_array($skill4, $enableSkill)) {
            $pokemon->skill4 = $skill4;
        }
        //確認技能是否相同
        $array = [];
        if ($pokemon->skill1 != null) {
            $array[] = $pokemon->skill1;
        }
        if ($pokemon->skill2 != null) {
            $array[] = $pokemon->skill2;
        }
        if ($pokemon->skill3 != null) {
            $array[] = $pokemon->skill3;
        }
        if ($pokemon->skill4 != null) {
            $array[] = $pokemon->skill4;
        }
        $uniqueArray = array_unique($array);
        if (count($array) != count($uniqueArray)) {
            return $this->res(400, [], "Skills cannot be repeated");
        }
        //回傳值
        $pokemon->save();
        $pokemonData = new PokemonResource($pokemon);
        return $this->res(201, $pokemonData, "Updated successfully");
    }
}
