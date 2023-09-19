<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * 測試搜尋寶可夢
     */
    public function test_query_pokemons()
    {
        $response = $this->get('/api/pokemon');
        $response->assertStatus(200);
    }
    /**
     * 測試新增寶可夢
     */
    public function test_add_pokemon()
    {
        $response = $this->post('/api/pokemon', [
            "name" => "pepepepe",
            "level" => "9",
            "race" => "ditto",
            "ability" => "正電",
            "nature" => "溫順"
        ]);
        $response->assertStatus(200);
    }
    /**
     * 測試搜尋單一寶可夢
     */
    public function test_query_single_pokemon()
    {
        $response = $this->get('/api/pokemon/13');
        $response->assertStatus(200);
    }
    /**
     * 測試更新寶可夢
     */
    public function test_update_pokemon()
    {
        $response = $this->put('/api/pokemon/13', [
            "name" => "pepefrog",
            "level" => "77",
            "ability" => "負電",
            "nature" => "溫順",
        ]);
        $response->assertStatus(200);
    }
    /**
     * 測試刪除寶可夢
     */
    public function test_delete_pokemon()
    {
        $response = $this->delete('/api/pokemon/13');
        $response->assertStatus(200);
    }
    /**
     * 測試新增特性
     */
    public function test_create_ability()
    {
        $response = $this->post('/api/ability', [
            "ability" => "太神啦"
        ]);
        $response->assertStatus(200);
    }
    /**
     * 測試更新特性
     */
    public function test_update_ability()
    {
        $response = $this->put('/api/ability/1341', [
            "ability" => "太神"
        ]);
        $response->assertStatus(200);
    }
    /**
     * 測試新增性格
     */
    public function test_create_nature()
    {
        $response = $this->post('/api/nature', [
            "nature" => "太神啦"
        ]);
        $response->assertStatus(200);
    }
    /**
     * 測試更新特性
     */
    public function test_update_nature()
    {
        $response = $this->put('/api/nature/1', [
            "nature" => "太神"
        ]);
        $response->assertStatus(200);
    }
    /**
     * 測試寶可夢可學習技能
     */
    public function test_query_pokemon_enable_skill()
    {
        $response = $this->get('/api/pokemon/7/enableSkill');
        $response->assertStatus(200);
    }
    /**
     * 測試寶可夢已學習技能
     */
    public function test_query_pokemon_skill()
    {
        $response = $this->get('/api/pokemon/7/skill');
        $response->assertStatus(200);
    }
    /**
     * 測試寶可夢學習技能
     */
    public function test_pokemon_learn_skill()
    {
        $response = $this->post('/api/pokemon/7/skill', [
            "skill1" => "double-edge",
            "skill2" => "growl",
            "skill3" => "roar",
            "skill4" => "hyper-beam"
        ]);
        $response->assertStatus(200);
    }
}
