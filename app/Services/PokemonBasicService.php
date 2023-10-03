<?php

namespace App\Services;

use App\Models\Pokemon;
use App\Http\Resources\PokemonResource;
use App\Http\Resources\PokemonCollection;
use App\Models\Ability;
use App\Models\Nature;
use Illuminate\Support\Facades\Auth;

class PokemonBasicService
{
    private $pokemonService;

    public function __construct(PokemonService $pokemonService)
    {
        $this->pokemonService = $pokemonService;
    }

    public function all_pokemon_query()
    {
        //取得可顯示寶可夢
        $pokemonQuery = Pokemon::where('status', true);
        //確認關鍵字
        if (!empty($query)) {
            $pokemonQuery->where(function ($_query) use ($query) {
                $_query->where("name", "like", "%" . $query . "%")
                    ->orWhere('race', 'like', "%" . $query . '%')
                    ->orWhere('level', 'like', "%" . $query . '%');
            });
            $pokemon = $pokemonQuery->get();
            //無相關寶可夢回傳
            if (count($pokemon) == 0) {
                return [];
            }
            $pokemonData = new PokemonCollection($pokemon);
            return $pokemonData;
        } else {
            //無關鍵字回傳所有寶可夢
            $pokemon = Pokemon::where('status', true)->get();
            $pokemonData = new PokemonCollection($pokemon);
            return $pokemonData;
        }
    }
    public function create_pokemon($request)
    {
        //取得寶可夢種族
        $race = $request->race;
        //取得登入ID
        $id = Auth::id();
        //確認寶可夢種族
        $data = $this->pokemonService->checkPokemonRace($race);
        if (empty($data)) {
            return [];
        }
        //確認是否進化
        $level = $request->level;
        $race = $this->pokemonService->checkPokemonEvolution($race, $level);
        //新增寶可夢
        $nature = Nature::where("name", $request->nature)->value("id");
        $ability = Ability::where("name", $request->ability)->value("id");
        $pokemon = Pokemon::create([
            "name" => $request->name,
            "level" => $level,
            "race" => $race,
            "nature_id" => $nature,
            "ability_id" => $ability,
            "user_id" => $id
        ]);
        //回傳資料格式
        $pokemonData = new PokemonResource($pokemon);
        return $pokemonData;
    }
    public function update_pokemon($request, $id)
    {
        //取得寶可夢
        $pokemon = Pokemon::find($id);
        //取得登入ID
        $id = Auth::id();
        //確認是否有寶可夢
        if ($pokemon == null || $pokemon->status == false) {
            return [404, [], "Pokemon does not exit"];
        }
        //判斷是否是使用者
        if ($pokemon->user_id != $id) {
            return [403, [], "Not the pokemon's user"];
        }
        $name = $request->input('name');
        $level = $request->input('level');
        $nature = $request->input('nature');
        $ability = $request->input('ability');

        //逐一確認更新資料
        if (!empty($name)) {
            $pokemon->name = $name;
        }
        //等級是否大於或小於
        if (!empty($level) && $level <= $pokemon->level) {
            $pokemon->level = $level;
        }
        if (!empty($level) && $level > $pokemon->level) {
            $pokemon->level = $level;
            $pokemon->race = $this->pokemonService->checkPokemonEvolution($pokemon->race, $level);
        }
        //確認性格
        if (!empty($nature)) {
            $pokemon->nature_id = Nature::where("name", $nature)->value("id");
        }
        //確認特性
        if (!empty($ability)) {
            $pokemon->ability_id = Ability::where("name", $ability)->value("id");
        }
        $pokemon->save();
        $pokemonData = new PokemonResource($pokemon);
        return [200, $pokemonData, "Update completed"];
    }
    public function delete_pokemon($id)
    {
        //查詢id
        $pokemon = Pokemon::find($id);
        //取得登入ID
        $id = Auth::id();
        //確認是否有寶可夢
        if ($pokemon == null) {
            return [404, [], "Pokemon does not exit"];
        }
        //判斷是否是使用者
        if ($pokemon->user_id != $id) {
            return [403, [], "Not the pokemon's user"];
        }
        //確認是否已刪除過
        if ($pokemon->status == false) {
            return [404, [], "Pokemon already deleted"];
        }
        //返回資料格式
        $pokemon->status = false;
        $pokemon->save();
        return [200, [], "successfully deleted"];
    }
}
