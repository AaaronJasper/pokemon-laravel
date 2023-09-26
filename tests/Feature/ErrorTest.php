<?php

namespace Tests\Feature;

use App\Models\Pokemon;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ErrorTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * 測試搜尋不存在寶可夢
     */
    public function test_query_not_exits_pokemons()
    {
        $response = $this->get('/api/pokemon/99');
        $response->assertStatus(200);
    }
    /**
     * 測試搜尋關鍵字無相關寶可夢
     */
    public function test_query_keyword_pokemons()
    {
        $response = $this->get('/api/pokemon/', [
            "query" => "a"
        ]);
        $response->assertStatus(200);
    }
    /**
     * 測試創建已存在特性
     */
    public function test_create_exits_ability()
    {
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->post('/api/ability', [
                "ability" => "負電"
            ]);
        $response->assertStatus(422);
    }
    /**
     * 測試更新不存在特性
     */
    public function test_update_not_exits_ability()
    {
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->put('/api/ability/1000', [
                "ability" => "過來一下"
            ]);
        $response->assertStatus(200);
    }
    /**
     * 測試創建已存在性格
     */
    public function test_create_exits_nature()
    {
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->post('/api/nature', [
                "nature" => "大膽"
            ]);
        $response->assertStatus(422);
    }
    /**
     * 測試更新不存在性格
     */
    public function test_update_not_exits_nature()
    {
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->put('/api/nature/9999', [
                "nature" => "過來一下"
            ]);
        $response->assertStatus(200);
    }
    /**
     * 測試創建不存在種族
     */
    public function test_create_not_exits_race()
    {
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        // 使用你的工廠方法來創建一個寶可夢
        $pokemon = Pokemon::factory()->create([
            'user_id' => $user->id,
        ]);
        $this->actingAs($user);
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->post('/api/pokemon', [
                "name" => "pepepe",
                "level" => "9",
                "race" => "dittoo",
                "ability" => "正電",
                "nature" => "溫順"
            ]);
        $response->assertStatus(200);
    }
    /**
     * 測試創建不存在性格
     */
    public function test_create_not_exits_nature()
    {
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        // 使用你的工廠方法來創建一個寶可夢
        $pokemon = Pokemon::factory()->create([
            'user_id' => $user->id,
        ]);
        $this->actingAs($user);
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->post('/api/pokemon', [
                "name" => "pepepe",
                "level" => "9",
                "race" => "ditto",
                "ability" => "正電",
                "nature" => "過來一下"
            ]);
        $response->assertStatus(422);
    }
    /**
     * 測試更新不存在寶可夢
     */
    public function test_update_not_exits_pokemon()
    {
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->put('/api/pokemon/99', [
                "name" => "pepepe",
                "level" => "9",
                "ability" => "正電",
                "nature" => "溫順"
            ]);
        $response->assertStatus(200);
    }
    /**
     * 測試更新不存在寶可夢性格
     */
    public function test_update_not_exits_pokemon_nature()
    {
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->withHeaders(['Accept' => 'application/json'])
            ->put('/api/pokemon/1', [
                "name" => "pepepe",
                "level" => "9",
                "ability" => "正電",
                "nature" => "過來一下"
            ]);
        $response->assertStatus(422);
    }
    /**
     * 測試刪除不存在寶可夢
     */
    public function test_delete_not_exits_pokemon()
    {
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->delete('/api/pokemon/99');
        $response->assertStatus(200);
    }
    /**
     * 測試查詢不存在寶可夢可學習技能
     */
    public function test_query_not_exits_pokemon_enable_skill()
    {
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get('/api/pokemon/99/enableSkill');
        $response->assertStatus(200);
    }
    /**
     * 測試查詢不存在寶可夢擁有學習技能
     */
    public function test_query_not_exits_pokemon_own_skill()
    {
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get('/api/pokemon/99/skill');
        $response->assertStatus(200);
    }
    /**
     * 測試不存在寶可夢學習技能
     */
    public function test_pokemon_learn_skill()
    {
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->post('/api/pokemon/4/skill', [
            "skill1" => "double-edge",
            "skill2" => "growl",
            "skill3" => "roar",
            "skill4" => "hyper-beam"
        ]);
        $response->assertStatus(200);
    }
    /**
     * 測試寶可夢學習相同技能
     */
    public function test_pokemon_learn_same_skill()
    {
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        // 使用你的工廠方法來創建一個寶可夢
        $pokemon = Pokemon::factory()->create([
            'user_id' => $user->id,
        ]);
        $this->actingAs($user);
        $response = $this->post('/api/pokemon/'.$pokemon->id."/skill", [
            "skill2" => "transform",
            "skill3" => "transform"
        ]);
        $response->assertStatus(200);
    }
    /**
     * 測試寶可夢學習非可學習技能
     */
    public function test_pokemon_learn_unable_skill()
    {
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        // 使用你的工廠方法來創建一個寶可夢
        $pokemon = Pokemon::factory()->create([
            'user_id' => $user->id,
        ]);
        $this->actingAs($user);
        $response = $this->post('/api/pokemon/'.$pokemon->id."/skill", [
            "skill2" => "cut",
        ]);
        $response->assertStatus(200);
    }
}
