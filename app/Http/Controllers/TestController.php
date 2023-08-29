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
        $response = $this->pokemonService->checkPokemonEvolution("bulbasaur" , 9);
        return $response;
    }
}
