<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PokemonService
{
    //確認種族是否存在
    public function checkPokemonRace($race)
    {
        //建構API请求URL
        $url = 'https://pokeapi.co/api/v2/pokemon/' . $race;
        //取得回傳資料
        $response = Http::get($url);
        $data = $response->json();
        return $data;
    }

    //確認是否進化
    public function checkPokemonEvolution(string $race, int $level)
    {
        //先取得進化鍊的url
        $url = 'https://pokeapi.co/api/v2/pokemon-species/' . $race;
        $response = Http::get($url);
        $data = $response->json();
        $evolutionChain = $data['evolution_chain']['url'];

        //取得進化資訊
        $evolutionResponse = Http::get($evolutionChain);
        $evolutionData = $evolutionResponse->json();

        //檢查是否進化
        $canEvolve = $this->checkEvolutionLevel($evolutionData, $level, $race);
        return $canEvolve;
    }

    //確認進化等級
    private function checkEvolutionLevel(array $evolutionData, int $level, string $race)
    {
        $chain = $evolutionData['chain'];

        while (!empty($chain["evolves_to"][0])) {
            $evlouLevel =  $chain["evolves_to"][0]['evolution_details'][0]["min_level"];
            if ($level >= $evlouLevel) {
                $race = $chain["evolves_to"][0]['species']['name'];
            }
            $chain =  $chain["evolves_to"][0];
        }
        return $race;
    }
}
