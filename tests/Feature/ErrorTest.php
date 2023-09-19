<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ErrorTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * 測試搜尋空寶可夢
     */
    public function test_query_not_exits_pokemons()
    {
        $response = $this->get('/api/pokemon/1');
        $response->assertStatus(200);
    }
    /**
     * 測試搜尋關鍵字寶可夢
     */
    public function test_query_keyword_pokemons()
    {
        $response = $this->get('/api/pokemon/', [
            "query" => "xyz"
        ]);
        $response->assertStatus(200);
    }
    /**
     * 測試創建已存在特性
     */
    public function test_create_exits_ability()
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->post('/api/ability', [
            "ability" => "滑起來"
        ]);
        $response->assertStatus(422);
    }
    /**
     * 測試創建已存在性格
     */
    public function test_create_exits_nature()
    {
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->post('/api/nature', [
            "nature" => "大膽"
        ]);
        $response->assertStatus(422);
    }
}
