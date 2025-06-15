<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\Trade;

class TradeService
{
    //find the trade in process
    public function findPendingTrade($userId)
    {
        $trade = Trade::where('status', 'pending')
            ->where(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId);
            })
            ->first();

        return $trade;
    }

    //find the trade in process by receiver
    public function findPendingTradeByReceiver($userId)
    {
        $trade = Trade::where('status', 'pending')
            ->where(function ($query) use ($userId) {
                $query->where('receiver_id', $userId);
            })
            ->first();

        return $trade;
    }
    
    //find the trade accompolished
    public function findAccomplishedTrade($userId)
    {
        $trade = Trade::where('status', 'accepted')
            ->where(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId);
            })
            ->get();

        return $trade;
    }

    //store new Trade
    public function createNewTrade($userId, $senderPokemon, $receiverPokemon)
    {
        $newTrade = Trade::create([
            'sender_id' => $userId,
            'receiver_id' => $receiverPokemon->user_id,
            'sender_pokemon_id' => $senderPokemon->id,
            'receiver_pokemon_id' => $receiverPokemon->id,
            'status' => 'pending',
        ]);

        $senderPokemon->is_trading = true;
        $senderPokemon->save();

        $receiverPokemon->is_trading = true;
        $receiverPokemon->save();

        return $newTrade; 
    }

    //reject trade
    public function rejectTrade($trade){

        $senderPokemon = $trade->senderPokemon;
        $receiverPokemon = $trade->receiverPokemon;

        $senderPokemon->is_trading = false;
        $receiverPokemon->is_trading = false;

        $senderPokemon->save();
        $receiverPokemon->save();

        $trade->status = 'rejected';
        $trade->save();
    }

    //execute trade
    public function executeTrade($trade){
        DB::transaction(function () use ($trade) {
            $senderPokemon = $trade->senderPokemon;
            $receiverPokemon = $trade->receiverPokemon;

            if (!$senderPokemon || !$receiverPokemon) {
                abort(404, 'One or both pokemons not found');
            }

            // 交換寶可夢歸屬
            $senderId = $trade->sender_id;
            $receiverId = $trade->receiver_id;

            $senderPokemon->user_id = $receiverId;
            $receiverPokemon->user_id = $senderId;

            $senderPokemon->is_trading = false;
            $receiverPokemon->is_trading = false;

            $senderPokemon->save();
            $receiverPokemon->save();

            $trade->status = 'accepted';
            $trade->save();
        });
    }

}
