<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Pokemon;

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

    //生成Pokemon 簡介
    public function generatePokemonDescription($id){
        
        $pokemon = Pokemon::find($id);

        $skills = array_filter([
            $pokemon->skill1,
            $pokemon->skill2,
            $pokemon->skill3,
            $pokemon->skill4,
        ]);

        $skillList = $skills ? implode(', ', $skills) : 'no known skills';

        $prompt = "Write a short English Wikipedia-style introduction (max 150 words) for a Pokémon named {$pokemon->name}. 
                It belongs to the race '{$pokemon->race}' and is currently at level {$pokemon->level}. 
                Its ability is '{$pokemon->ability}' and its nature is '{$pokemon->nature}'. 
                It has learned the following skills: {$skillList}.
                Use an informative and neutral tone, similar to an encyclopedia article.";

        $systemPrompt = "You are an expert Pokémon researcher writing encyclopedia entries in the style of Wikipedia. Keep entries under 150 words.";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openrouter.key'),
            'HTTP-Referer' => 'http://127.0.0.1:3000', 
            'Content-Type' => 'application/json',
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            'model' => 'openai/gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => 300,
        ]);
        
        $data = $response->json();

        $content = $data['choices'][0]['message']['content'] ?? '';

        return response()->json([
            'description' => $content,
        ]);
    }
}


