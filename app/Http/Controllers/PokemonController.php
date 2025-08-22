<?php

namespace App\Http\Controllers;

use App\Http\Requests\GeneratePokemonDescriptionRequest;
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
        $this->middleware('auth:sanctum')->except(["index", "show", 'get_pokemon_picture', 'generateDescription']);
    }

    /**
     * Search all Pokemons
     * 
     * Retrieve a list of all Pokémons. Authentication is optional – if a valid token is provided,
     * the response will also indicate whether the current user has liked each Pokémon.
     * 
     * @header Authorization Bearer {token} optional Example: Bearer 196|wQr6eQ7dvE2cjGyztjIWJeGqCWa0GhVSON2Z7EcC
     * 
     * @response {
     *   "code": 201,
     *   "data": {
     *     "data": [
     *       {
     *         "id": 5,
     *         "name": "pepe99",
     *         "level": "1",
     *         "race": "wugtrio",
     *         "nature": "creative",
     *         "ability": "versatile",
     *         "skill1": "slam",
     *         "skill2": "sand-attack",
     *         "skill3": "headbutt",
     *         "skill4": "wrap",
     *         "created_at": "2023-09-20 09:24:18",
     *         "updated_at": "2023-09-20 09:24:18",
     *         "image_url": "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/961.png",
     *         "is_liked": false
     *       },
     *       {
     *         "id": 6,
     *         "name": "pepe100",
     *         "level": "2",
     *         "race": "wugtrio",
     *         "nature": "creative",
     *         "ability": "versatile",
     *         "skill1": "slam",
     *         "skill2": "sand-attack",
     *         "skill3": "headbutt",
     *         "skill4": "wrap",
     *         "created_at": "2023-09-21 10:00:00",
     *         "updated_at": "2023-09-21 10:00:00",
     *         "image_url": "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/961.png",
     *         "is_liked": false 
     *       }
     *     ]
     *   },
     *   "message": "Created successfully"
     * }
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
     * 
     * Save a new Pokémon to the database with its attributes and initial skills.
     * 
     * @authenticated
     * @header Authorization Bearer {token} Example: Bearer 196|wQr6eQ7dvE2cjGyztjIWJeGqCWa0GhVSON2Z7EcC
     * @bodyParam name string required Must be at least 1 character. Max 8 characters. Example: pepe99
     * @bodyParam level integer required Min: 1, Max: 100. Example: 1
     * @bodyParam ability string The name of the ability required Example: creative
     * @bodyParam nature string The name of the nature required Example: verstaile
     * 
     * @response{
     * "code": 201,
     * "data": {
     *     "id": 5,
     *     "name": "pepe99",
     *     "level": "1",
     *     "race": "wugtrio",
     *     "nature": "creative",
     *     "ability": "versatile",
     *     "skill1": null,
     *     "skill2": null,
     *     "skill3": null,
     *     "skill4": null,
     *     "created_at": "2023-09-20 09:24:18",
     *     "updated_at": "2023-09-20 09:24:18",
     *     "image_url": "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/961.png",
     *     "is_liked": false 
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
     * 
     * Retrieve detailed information about a specific Pokémon by its ID, including skills, stats, and like status.
     * 
     * @urlParam id int required The ID of the Pokemon. Example: 5
     * 
     * @response{
     * "code": 200,
     * "data": {
     *     "id": 5,
     *     "name": "pepe99",
     *     "level": "11",
     *     "race": "wugtrio",
     *     "nature": "creative",
     *     "ability": "paitent",
     *     "skill1": "slam",
     *     "skill2": "sand-attack",
     *     "skill3": "headbutt",
     *     "skill4": "wrap",
     *     "created_at": "2023-09-20 09:24:18",
     *     "updated_at": "2023-09-20 09:24:18",
     *     "image_url": "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/961.png",
     *     "is_liked": false
     * },
     * "message": "search successfully"
     * }
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
     * 
     * Update the attributes of an existing Pokémon. Authentication is required.
     * 
     * @authenticated
     * @header Authorization Bearer {token} Example: Bearer 196|wQr6eQ7dvE2cjGyztjIWJeGqCWa0GhVSON2Z7EcC
     * @urlParam id int required The ID of the Pokemon. Example: 5
     * @bodyParam name string Must be at least 1 character. Max 8 characters. Example: pepe99
     * @bodyParam level integer Min: 1, Max: 100. Example: 5
     * @bodyParam ability string The name of the ability Example: creative
     * @bodyParam nature string The name of the nature Example: versatile
     * 
     * @response{
     * "code": 200,
     * "data": {
     *     "id": 5,
     *     "name": "pepe99",
     *     "level": "11",
     *     "race": "wugtrio",
     *     "nature": "creative",
     *     "ability": "paitent",
     *     "skill1": "slam",
     *     "skill2": "sand-attack",
     *     "skill3": "headbutt",
     *     "skill4": "wrap",
     *     "created_at": "2023-09-20 09:24:18",
     *     "updated_at": "2023-09-20 09:24:18",
     *     "image_url": "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/961.png",
     *     "is_liked": false
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
     * 
     * Remove a Pokémon from the system. Authentication is required.
     * 
     * @authenticated
     * @header Authorization Bearer {token} Example: Bearer 196|wQr6eQ7dvE2cjGyztjIWJeGqCWa0GhVSON2Z7EcC 
     * @urlParam id int required The ID of the Pokemon. Example: 5
     * 
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

    /**
     * Generate Pokemon description
     * 
     * Generate a detailed description for a Pokémon based on its ID, including its level, ability, nature, and moves.
     * 
     * @bodyParam pokemon_id int required The ID of the Pokemon. Example: 132
     *
     * @response {
     *   "code": 200,
     *   "data": {
     *     "headers": {},
     *     "original": {
     *       "description": "Wugtrio is a high-level Pokémon species known for its distinctive traits and abilities. Currently at level 99, Wugtrio possesses the unique ability named \"Patient,\" which reflects its strategic nature and allows it to outlast opponents in battles. This Pokémon exhibits a creative nature, often utilizing unconventional methods to tackle challenges and opponents alike.\n\nWugtrio has mastered a versatile array of moves, including Slam, Sand-Attack, Headbutt, and Wrap, each contributing to its adaptability and strength in combat. These skills highlight Wugtrio's capability to both deliver powerful attacks and employ strategic defensive maneuvers. Its combination of high-level experience and diverse move set make it a formidable opponent in various battle scenarios. Known for its resilience and ingenuity, Wugtrio exemplifies strategic depth, making it a valuable asset for trainers seeking a versatile and resilient partner."
     *     },
     *     "exception": null
     *   },
     *   "message": "Generate successfully"
     * }
     */
    public function generateDescription(GeneratePokemonDescriptionRequest $request){
        
        $response = $this->pokemonService->generatePokemonDescription($request->pokemon_id);
        
        if (!$response) {
            return $this->res(500, "", "Fail to generate");
        }

        return $this->res(200, $response, "Generate successfully");
    }
}
