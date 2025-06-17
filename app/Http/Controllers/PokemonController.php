<?php

namespace App\Http\Controllers;

use App\Http\Requests\PokemonReuqest;
use App\Http\Requests\PokemonUpdateRequest;
use App\Http\Resources\PokemonResource;
use Illuminate\Support\Facades\Auth;
use App\Models\Pokemon;
use App\Models\User;
use App\Services\PokemonBasicService;
use App\Services\PokemonService;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;



class PokemonController extends BaseController
{
    private $pokemonService;
    private $pokemonBasicService;

    public function __construct(PokemonService $pokemonService, PokemonBasicService $pokemonBasicService)
    {
        $this->pokemonService = $pokemonService;
        $this->pokemonBasicService = $pokemonBasicService;
        $this->middleware('auth:sanctum')->except(["index", "show", 'get_pokemon_picture']);
    }

    /**
     * Search all Pokemons
     */
    public function index(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        
        //取得關鍵字
        $query = $request->input('query');
        $pokemonData = $this->pokemonBasicService->all_pokemon_query($query, $user);
        return $this->res(200, $pokemonData, "search successfully");
    }

    /**
     * Store a Pokemon
     * @response{
     * "code": 201,
     * "data": {
     *     "id": 5,
     *     "name": "pepe99",
     *     "level": "1",
     *     "race": "ditto",
     *     "nature": "天真",
     *     "ability": "惡臭",
     *     "skill1": null,
     *     "skill2": null,
     *     "skill3": null,
     *     "skill4": null,
     *     "created_at": "2023-09-20 09:24:18",
     *     "updated_at": "2023-09-20 09:24:18"
     * },
     * "message": "Created successfully"
     * }
     */
    public function store(PokemonReuqest $request)
    {
        $pokemonData = $this->pokemonBasicService->create_pokemon($request);
        if ($pokemonData == []) {
            return $this->res(404, $pokemonData, "Race does not exit");
        } else {
            return $this->res(201, $pokemonData, "Created successfully");
        }
    }

    /**
     * Search a Pokemon
     */
    public function show(string $id)
    {
        $user = Auth::guard('sanctum')->user();
        //查詢id
        $pokemon = Pokemon::find($id);
        if ($pokemon == null or $pokemon->status == false) {
            return $this->res(404, [], "Pokemon does not exit");
        }

        $likedIds = $user?->likedPokemons()->pluck('pokemon_id')->toArray() ?? [];
        $pokemon->is_liked = in_array($pokemon->id, $likedIds);
        //返回資料格式
        $pokemonData = new PokemonResource($pokemon);
        return $this->res(200, $pokemonData, "search successfully");
    }

    /**
     * Update a Pokemon
     * @response{
     * "code": 201,
     * "data": {
     *     "id": 5,
     *     "name": "pepe99",
     *     "level": "11",
     *     "race": "ditto",
     *     "nature": "creative",
     *     "ability": "paitent",
     *     "skill1": null,
     *     "skill2": null,
     *     "skill3": null,
     *     "skill4": null,
     *     "created_at": "2023-09-20 09:24:18",
     *     "updated_at": "2023-09-20 09:24:18"
     * },
     * "message": "Update completed"
     * }
     */
    public function update(PokemonUpdateRequest $request, string $id)
    {
        $pokemonData = $this->pokemonBasicService->update_pokemon($request, $id);
        return $this->res($pokemonData[0], $pokemonData[1], $pokemonData[2]);
    }

    /**
     * Delete a Pokemon
     * @response{
     * "code": 200,
     * "data": [],
     * "message": "successfully deleted"
     * }
     */
    public function destroy(string $id)
    {
        $pokemonData = $this->pokemonBasicService->delete_pokemon($id);
        return $this->res($pokemonData[0], $pokemonData[1], $pokemonData[2]);
    }
}
