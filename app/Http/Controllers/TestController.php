<?php

namespace App\Http\Controllers;

use App\Services\PokemonService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class TestController extends Controller
{
    private $pokemonService;
    public function __construct(PokemonService $pokemonService)
    {
        $this->pokemonService = $pokemonService;
    }

    public function test()
    {
        $response = Http::get("https://pokeapi.co/api/v2/pokemon/charizard");

        $imageData = $response['sprites']['front_default'];

        if (!$imageData) {
            return null;
        }

        return $imageData;
    }

    public function index()
    {
        return "test2";
    }
}
