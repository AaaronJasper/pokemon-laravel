<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Pokemon;
use App\Models\User;
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
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        $this->actingAs($user);
        //加入值
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
        $response = $this->get('/api/pokemon/1');
        $response->assertStatus(200);
    }
    /**
     * 測試更新寶可夢
     */
    public function test_update_pokemon()
    {
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        // 使用你的工廠方法來創建一個寶可夢
        $pokemon = Pokemon::factory()->create([
            'user_id' => $user->id,
        ]);
        $this->actingAs($user);
        $response = $this->put('/api/pokemon/' . $pokemon->id, [
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
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        // 使用你的工廠方法來創建一個寶可夢
        $pokemon = Pokemon::factory()->create([
            'user_id' => $user->id,
        ]);
        $this->actingAs($user);
        $response = $this->delete('/api/pokemon/' . $pokemon->id);
        $response->assertStatus(200);
    }
    /**
     * 測試新增特性
     */
    public function test_create_ability()
    {
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        $this->actingAs($user);
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
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        $this->actingAs($user);
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
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        $this->actingAs($user);
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
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        $this->actingAs($user);
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
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        // 使用你的工廠方法來創建一個寶可夢
        $pokemon = Pokemon::factory()->create([
            'user_id' => $user->id,
        ]);
        $this->actingAs($user);
        $response = $this->get('/api/pokemon/' . $pokemon->id . "/enableSkill");
        $response->assertStatus(200);
    }
    /**
     * 測試寶可夢已學習技能
     */
    public function test_query_pokemon_skill()
    {
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        // 使用你的工廠方法來創建一個寶可夢
        $pokemon = Pokemon::factory()->create([
            'user_id' => $user->id,
        ]);
        $this->actingAs($user);
        $response = $this->get('/api/pokemon/' . $pokemon->id . "/skill");
        $response->assertStatus(200);
    }
    /**
     * 測試寶可夢學習技能
     */
    public function test_pokemon_learn_skill()
    {
        // 使用你的工廠方法來創建一個使用者
        $user = User::factory()->create();
        // 使用你的工廠方法來創建一個寶可夢
        $pokemon = Pokemon::factory()->create([
            'user_id' => $user->id,
        ]);
        $this->actingAs($user);
        $response = $this->post('/api/pokemon/' . $pokemon->id . "/skill", [
            "skill1" => "transform",
        ]);
        $response->assertStatus(200);
    }
}
