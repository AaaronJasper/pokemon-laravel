<?php

namespace App\Http\Controllers;

use App\Http\Requests\PokemonReuqest;
use App\Http\Requests\PokemonUpdateRequest;
use App\Http\Resources\PokemonCollection;
use App\Http\Resources\PokemonResource;
use App\Models\Ability;
use App\Models\Nature;
use App\Models\Pokemon;
use App\Services\PokemonService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class PokemonController extends BaseController
{
    private $pokemonService;

    public function __construct(PokemonService $pokemonService)
    {
        $this->pokemonService = $pokemonService;
    }

    /**
     * 查詢寶可夢
     */
    public function index(Request $request)
    {
        //取得關鍵字
        $query = $request->input('query');
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
                return $this->res(200, [], "No related Pokémon");
            }
            $pokemonData = new PokemonCollection($pokemon);
            return $this->res(200, $pokemonData, "search successfully");
        } else {
            //無關鍵字回傳所有寶可夢
            $pokemon = Pokemon::where('status', true)->get();
            $pokemonData = new PokemonCollection($pokemon);
            return $this->res(200, $pokemonData, "search successfully");
        }
    }

    /**
     *新增寶可夢
     */
    public function store(PokemonReuqest $request)
    {
        //取得寶可夢種族
        $race = $request->race;
        //確認寶可夢種族
        $data = $this->pokemonService->checkPokemonRace($race);
        if (empty($data)) {
            return $this->res(404, [], "Race does not exit");
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
            "ability_id" => $ability
        ]);
        //回傳資料格式
        $pokemonData = new PokemonResource($pokemon);
        return $this->res(201, $pokemonData, "Created successfully");
    }

    /**
     * 查詢單一寶可夢
     */
    public function show(string $id)
    {
        //查詢id
        $pokemon = Pokemon::find($id);
        if ($pokemon == null or $pokemon->status == false) {
            return $this->res(404, [], "Pokemon does not exit");
        }
        //返回資料格式
        $pokemonData = new PokemonResource($pokemon);
        return $this->res(200, $pokemonData, "search successfully");
    }

    /**
     * 更新寶可夢
     */
    public function update(PokemonUpdateRequest $request, string $id)
    {
        //取得寶可夢
        $pokemon = Pokemon::find($id);
        if ($pokemon->status == false) {
            return $this->res(404, [], "Pokemon does not exit");
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
        return $this->res(200, $pokemonData, "Update completed");
    }

    /**
     * 刪除寶可夢
     */
    public function destroy(string $id)
    {
        //查詢id
        $pokemon = Pokemon::find($id);
        if ($pokemon == null) {
            return $this->res(404, [], "Pokemon does not exit");
        }
        //確認是否已刪除過
        if ($pokemon->status == false) {
            return $this->res(404, [], "Pokemon already deleted");
        }
        //返回資料格式
        $pokemon->status = false;
        $pokemon->save();
        return $this->res(200, [], "successfully deleted");
    }
}
