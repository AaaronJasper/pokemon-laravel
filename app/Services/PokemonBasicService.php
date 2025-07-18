<?php

namespace App\Services;

use App\Models\Pokemon;
use App\Http\Resources\PokemonResource;
use App\Http\Resources\PokemonCollection;
use App\Models\Ability;
use App\Models\Nature;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class PokemonBasicService
{
    private $pokemonService;

    public function __construct(PokemonService $pokemonService)
    {
        $this->pokemonService = $pokemonService;
    }

    public function all_pokemon_query($query, $user)
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
            $pokemons = $pokemonQuery->get();
            //無相關寶可夢回傳
            if (count($pokemons) == 0) {
                return [];
            }
            
            $likedIds = $user?->likedPokemons()->pluck('pokemon_id')->toArray() ?? [];

            // 加上 is_liked 欄位（沒有登入的話，全部 false）
            $pokemons->transform(function ($pokemon) use ($likedIds) {
                $pokemon->is_liked = in_array($pokemon->id, $likedIds);
                return $pokemon;
            });

            $pokemonData = new PokemonCollection($pokemons);
            return $pokemonData;
        } else {
            //無關鍵字回傳所有寶可夢
            $pokemons = Pokemon::where('status', true)->get();

            $likedIds = $user?->likedPokemons()->pluck('pokemon_id')->toArray() ?? [];

            // 加上 is_liked 欄位（沒有登入的話，全部 false）
            $pokemons->transform(function ($pokemon) use ($likedIds) {
                $pokemon->is_liked = in_array($pokemon->id, $likedIds);
                return $pokemon;
            });

            $pokemonData = new PokemonCollection($pokemons);
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
        //拿取pokemon picture url
        $url = $this->getPokemonPicture($race);
        //新增寶可夢
        $nature = Nature::where("name", $request->nature)->value("id");
        $ability = Ability::where("name", $request->ability)->value("id");
        $pokemon = Pokemon::create([
            "name" => $request->name,
            "level" => $level,
            "race" => strtolower($race),
            "nature_id" => $nature,
            "ability_id" => $ability,
            "user_id" => $id,
            "image_url" => $url
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
        $user = Auth::user();
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
            $prevRace = $pokemon->race;
            $pokemon->race = $this->pokemonService->checkPokemonEvolution($pokemon->race, $level);
            
            if ($prevRace != $pokemon->race){
                $pokemon->image_url = $this->getPokemonPicture($pokemon->race);
            }
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

        $likedIds = $user?->likedPokemons()->pluck('pokemon_id')->toArray() ?? [];
        $pokemon->is_liked = in_array($pokemon->id, $likedIds);
        
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

    public function getPokemonPicture(string $race)
    {
        $response = Http::get("https://pokeapi.co/api/v2/pokemon/" . strtolower($race));

        $imageData = $response['sprites']['front_default'];

        if (!$imageData) {
            return null;
        }

        return $imageData;
    }
}
