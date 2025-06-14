<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\Trade;
use App\Models\Pokemon;
use Tests\TestCase;

class TradeTest extends TestCase
{
    use DatabaseTransactions;
    /**
     * test initiate trade
     */
    public function test_initiate_trade(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $pokemonA = Pokemon::factory()->create(['user_id' => $userA->id]);
        $pokemonB = Pokemon::factory()->create(['user_id' => $userB->id]);

        $response = $this->actingAs($userA)->post('/api/trade', [
            'sender_pokemon_id' => $pokemonA->id,
            'receiver_pokemon_id' => $pokemonB->id,
        ]);

        $response->assertStatus(200)->assertJson([
            "code" => 201,
            'message' => "Initiate trade successfully",
        ]);

        $this->assertDatabaseHas('trades', [
            'sender_id' => $userA->id,
            'receiver_id' => $userB->id,
            'sender_pokemon_id' => $pokemonA->id,
            'receiver_pokemon_id' => $pokemonB->id,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('pokemon', [
            'id' => $pokemonA->id,
            'user_id' => $userA->id,
        ]);
        $this->assertDatabaseHas('pokemon', [
            'id' => $pokemonB->id,
            'user_id' => $userB->id,
        ]);
    }

    /**
     * test search for the trade 
     */
    public function test_show_trade()
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $pokemonA = Pokemon::factory()->create(['user_id' => $userA->id]);
        $pokemonB = Pokemon::factory()->create(['user_id' => $userB->id]);

        $trade = Trade::create([
            'sender_id' => $userA->id,
            'receiver_id' => $userB->id,
            'sender_pokemon_id' => $pokemonA->id,
            'receiver_pokemon_id' => $pokemonB->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($userA)->getJson("/api/trade/");

        $response->assertStatus(200)
            ->assertJson([
                'code' => 200,
                'data' => [
                    'id' => $trade->id,
                    'sender_id' => $userA->id,
                    'receiver_id' => $userB->id,
                    'sender_pokemon_id' => $pokemonA->id,
                    'receiver_pokemon_id' => $pokemonB->id,
                    'status' => 'pending',
                ],
            ]);
    }

    /**
     * test show no pending trade
     */
    public function test_show_trade_not_found()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson("/api/trade/");

        $response->assertStatus(200)
            ->assertJson([
                'code' => 404,
                'data' => [],
                'message' => 'No pending trade found',
            ]);
    }

    /**
     * test accept trade
     */
    public function test_accept_trade_and_swap_pokemons(): void
    {
        $userA = User::factory()->create(); 
        $userB = User::factory()->create(); 

        $pokemonA = Pokemon::factory()->create(['user_id' => $userA->id]);
        $pokemonB = Pokemon::factory()->create(['user_id' => $userB->id]);

        $trade = Trade::create([
            'sender_id' => $userA->id,
            'receiver_id' => $userB->id,
            'sender_pokemon_id' => $pokemonA->id,
            'receiver_pokemon_id' => $pokemonB->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($userB)->putJson("/api/trade/{$trade->id}/accept");

        $response->assertStatus(200)
                ->assertJson([
                    'code' => 200,
                    'message' => 'Trade completed successfully',
                ]);

        $this->assertDatabaseHas('trades', [
            'id' => $trade->id,
            'status' => 'accepted',
        ]);

        $this->assertDatabaseHas('pokemon', [
            'id' => $pokemonA->id,
            'user_id' => $userB->id, 
        ]);

        $this->assertDatabaseHas('pokemon', [
            'id' => $pokemonB->id,
            'user_id' => $userA->id, 
        ]);
    }

    public function test_reject_trade(): void
    {
       
        $userA = User::factory()->create(); 
        $userB = User::factory()->create(); 

        $pokemonA = Pokemon::factory()->create(['user_id' => $userA->id]);
        $pokemonB = Pokemon::factory()->create(['user_id' => $userB->id]);

        $trade = Trade::create([
            'sender_id' => $userA->id,
            'receiver_id' => $userB->id,
            'sender_pokemon_id' => $pokemonA->id,
            'receiver_pokemon_id' => $pokemonB->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($userB)->putJson("/api/trade/{$trade->id}/reject");

        $response->assertStatus(200)
                ->assertJson([
                    'code' => 200,
                    'message' => 'Trade rejected successfully',
                ]);

        $this->assertDatabaseHas('trades', [
            'id' => $trade->id,
            'status' => 'rejected',
        ]);

        $this->assertDatabaseHas('pokemon', [
            'id' => $pokemonA->id,
            'user_id' => $userA->id,
        ]);

        $this->assertDatabaseHas('pokemon', [
            'id' => $pokemonB->id,
            'user_id' => $userB->id,
        ]);
    }
}
