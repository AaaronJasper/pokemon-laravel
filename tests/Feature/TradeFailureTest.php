<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\Trade;
use App\Models\Pokemon;
use Tests\TestCase;

class TradeFailureTest extends TestCase
{
    use DatabaseTransactions;

    public function test_cannot_trade_if_sender_pokemon_is_inactive()
    {
        $user = User::factory()->create();
        $partner = User::factory()->create();

        $senderPokemon = Pokemon::factory()->create(['user_id' => $user->id, 'status' => 0]);
        $receiverPokemon = Pokemon::factory()->create(['user_id' => $partner->id]);

        $this->actingAs($user)
            ->postJson('/api/trade', [
                'sender_pokemon_id' => $senderPokemon->id,
                'receiver_pokemon_id' => $receiverPokemon->id,
            ])
            ->assertStatus(200)
            ->assertJson([
                'code' => 403,
                'message' => 'Pokemon can not be traded',
            ]);
    }

    public function test_cannot_trade_if_sender_pokemon_is_trading()
    {
        $user = User::factory()->create();
        $partner = User::factory()->create();

        $senderPokemon = Pokemon::factory()->create(['user_id' => $user->id, 'is_trading' => 1]);
        $receiverPokemon = Pokemon::factory()->create(['user_id' => $partner->id]);

        $this->actingAs($user)
            ->postJson('/api/trade', [
                'sender_pokemon_id' => $senderPokemon->id,
                'receiver_pokemon_id' => $receiverPokemon->id,
            ])
            ->assertStatus(200)
            ->assertJson([
                'code' => 403,
                'message' => 'Your pokemon is in trading process',
            ]);
    }

    public function test_cannot_trade_if_receiver_pokemon_is_trading()
    {
        $user = User::factory()->create();
        $partner = User::factory()->create();

        $senderPokemon = Pokemon::factory()->create(['user_id' => $user->id]);
        $receiverPokemon = Pokemon::factory()->create(['user_id' => $partner->id, 'is_trading' => 1]);

        $this->actingAs($user)
            ->postJson('/api/trade', [
                'sender_pokemon_id' => $senderPokemon->id,
                'receiver_pokemon_id' => $receiverPokemon->id,
            ])
            ->assertStatus(200)
            ->assertJson([
                'code' => 403,
                'message' => "Partner's pokemon is in trading process",
            ]);
    }

    public function test_cannot_trade_if_sender_pokemon_not_owned_by_user()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $partner = User::factory()->create();

        $senderPokemon = Pokemon::factory()->create(['user_id' => $otherUser->id]);
        $receiverPokemon = Pokemon::factory()->create(['user_id' => $partner->id]);

        $this->actingAs($user)
            ->postJson('/api/trade', [
                'sender_pokemon_id' => $senderPokemon->id,
                'receiver_pokemon_id' => $receiverPokemon->id,
            ])
            ->assertStatus(200)
            ->assertJson([
                'code' => 403,
                'message' => 'You can only trade your own pokemon',
            ]);
    }

    public function test_cannot_trade_with_self()
    {
        $user = User::factory()->create();

        $senderPokemon = Pokemon::factory()->create(['user_id' => $user->id]);
        $receiverPokemon = Pokemon::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->postJson('/api/trade', [
                'sender_pokemon_id' => $senderPokemon->id,
                'receiver_pokemon_id' => $receiverPokemon->id,
            ])
            ->assertStatus(200)
            ->assertJson([
                'code' => 403,
                'message' => 'You can not trade with youself',
            ]);
    }

    public function test_cannot_trade_if_user_already_has_pending_trade()
    {
        $user = User::factory()->create();
        $partner = User::factory()->create();

        $senderPokemon = Pokemon::factory()->create(['user_id' => $user->id]);
        $receiverPokemon = Pokemon::factory()->create(['user_id' => $partner->id]);

        $trade = Trade::create([
            'sender_id' => $user->id,
            'receiver_id' => $partner->id,
            'sender_pokemon_id' => $senderPokemon->id,
            'receiver_pokemon_id' => $receiverPokemon->id,
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->postJson('/api/trade', [
                'sender_pokemon_id' => $senderPokemon->id,
                'receiver_pokemon_id' => $receiverPokemon->id,
            ])
            ->assertStatus(200)
            ->assertJson([
                'code' => 403,
                'message' => 'Already has a pending trade',
            ]);
    }
}