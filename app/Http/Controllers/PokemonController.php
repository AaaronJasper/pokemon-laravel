<?php

namespace App\Http\Controllers;

use App\Http\Requests\PokemonReuqest;
use App\Http\Requests\PokemonUpdateRequest;
use App\Http\Resources\PokemonResource;
use App\Models\Pokemon;
use App\Services\PokemonBasicService;
use App\Services\PokemonService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class PokemonController extends BaseController
{
    private $pokemonService;
    private $pokemonBasicService;

    public function __construct(PokemonService $pokemonService, PokemonBasicService $pokemonBasicService)
    {
        $this->pokemonService = $pokemonService;
        $this->pokemonBasicService = $pokemonBasicService;
        $this->middleware('auth:sanctum')->except(["index", "show"]);
    }

    /**
     * 查詢寶可夢
     */
    public function index(Request $request)
    {
        //取得關鍵字
        $query = $request->input('query');
        $pokemonData = $this->pokemonBasicService->all_pokemon_query($query);
        return $this->res(200, $pokemonData, "search successfully");
    }

    /**
     *新增寶可夢
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
     * @response{
     * "code": 201,
     * "data": {
     *     "id": 5,
     *     "name": "pepe99",
     *     "level": "11",
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
     * "message": "Update completed"
     * }
     */
    public function update(PokemonUpdateRequest $request, string $id)
    {
        $pokemonData = $this->pokemonBasicService->update_pokemon($request, $id);
        return $this->res($pokemonData[0], $pokemonData[1], $pokemonData[2]);
    }

    /**
     * 刪除寶可夢
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
