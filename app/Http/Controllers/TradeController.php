<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTradeRequest;
use App\Models\Trade;
use App\Models\Pokemon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TradeController extends BaseController
{
    // initiate a trade
    public function store(StoreTradeRequest $request)
    {
        $senderPokemon = Pokemon::find($request->sender_pokemon_id);
        $receiverPokemon = Pokemon::find($request->receiver_pokemon_id);

        if ($senderPokemon->status == 0 or $receiverPokemon->status == 0){
            return $this->res(403, [], "Pokemon can not be traded");
        }
        
        if ($senderPokemon->is_trading == 1 or $receiverPokemon->is_trading == 1){
            return $this->res(403, [], "Pokemon is in trading process");
        }

        if ($senderPokemon->user_id !== auth()->id()) {
            return $this->res(403, [], "You can only trade your own pokemon");
        }

        if ($receiverPokemon->user_id === auth()->id()) {
            return $this->res(403, [], "You can not trade with youself");
        }

        $userId = Auth::id();

        $trade = Trade::where('status', 'pending')
            ->where(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId);
            })
            ->first();

        if ($trade){
            return $this->res(403, [], "Already has a pending trade");
        }

        $trade = Trade::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $receiverPokemon->user_id,
            'sender_pokemon_id' => $senderPokemon->id,
            'receiver_pokemon_id' => $receiverPokemon->id,
            'status' => 'pending',
        ]);

        $senderPokemon->is_trading = true;
        $senderPokemon->save();

        $receiverPokemon->is_trading = true;
        $receiverPokemon->save();

        return $this->res(201, $trade, "Initiate trade successfully");
    }

    //show a trade
    public function show()
    {
        $userId = Auth::id();

        $trade = Trade::where('status', 'pending')
            ->where(function ($query) use ($userId) {
                $query->where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId);
            })
            ->first();

        if (!$trade) {
            return $this->res(404, [], 'No pending trade found');
        }

        return $this->res(200, $trade, '');
    }

    //accept trade
    public function accept($id)
    {
        $userId = Auth::id();

        $trade = Trade::where('id', $id)
            ->where('status', 'pending')
            ->where('receiver_id', $userId)
            ->first();

        if (!$trade) {
            return $this->res(404, [], 'Trade not found or not allowed');
        }

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

        return $this->res(200, $trade, 'Trade accepted successfully');
    }

    //reject trade
    public function reject($id)
    {
        $userId = Auth::id();

        $trade = Trade::where('id', $id)
            ->where('status', 'pending')
            ->where('receiver_id', $userId)
            ->first();

        if (!$trade) {
            return $this->res(404, [], 'Trade not found or not allowed to reject');
        }

        $senderPokemon = $trade->senderPokemon;
        $receiverPokemon = $trade->receiverPokemon;

        $senderPokemon->is_trading = false;
        $receiverPokemon->is_trading = false;

        $senderPokemon->save();
        $receiverPokemon->save();

        $trade->status = 'rejected';
        $trade->save();

        return $this->res(200, $trade, 'Trade rejected successfully');
    }
}
