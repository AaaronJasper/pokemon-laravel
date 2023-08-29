<?php

namespace App\Http\Controllers;

use App\Services\PokemonService;
use Illuminate\Http\Request;

class TestController extends Controller
{
    private $pokemonService;
    public function __construct(PokemonService $pokemonService)
    {
        $this->pokemonService = $pokemonService;
    }

    public function test()
    {
        return "test2";
        $response = $this->pokemonService->checkPokemonEvolution("bulbasaur" , 9);
        return $response;
    }

    public function index()
    {
        return "test2";
    }
}
