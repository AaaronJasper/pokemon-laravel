<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTradeRequest;
use App\Models\Pokemon;
use Illuminate\Support\Facades\Auth;
use App\Services\TradeService;

class TradeController extends BaseController
{
    private $tradeService;

    public function __construct(TradeService $tradeService)
    {
        $this->tradeService = $tradeService;
    }

    // initiate a trade
    public function store(StoreTradeRequest $request)
    {
        $senderPokemon = Pokemon::find($request->sender_pokemon_id);
        $receiverPokemon = Pokemon::find($request->receiver_pokemon_id);

        if ($senderPokemon->status == 0 or $receiverPokemon->status == 0){
            return $this->res(403, [], "Pokemon can not be traded");
        }
        
        if ($senderPokemon->is_trading == 1){
            return $this->res(403, [], "Your pokemon is in trading process");
        }

        if ($receiverPokemon->is_trading == 1){
            return $this->res(403, [], "Partner's pokemon is in trading process");
        }

        if ($senderPokemon->user_id !== auth()->id()) {
            return $this->res(403, [], "You can only trade your own pokemon");
        }

        if ($receiverPokemon->user_id === auth()->id()) {
            return $this->res(403, [], "You can not trade with youself");
        }

        $userId = Auth::id();

        $trade = $this->tradeService->findPendingTrade($userId);

        if ($trade){
            return $this->res(403, [], "Already has a pending trade");
        }

        $newTrade = $trade = $this->tradeService->createNewTrade($userId, $senderPokemon, $receiverPokemon);

        if ($newTrade){
            return $this->res(201, $newTrade, "Initiate trade successfully");
        }
        else{
            return $this->res(403, [], "Fail initiate trade ");
        }
    }

    //show a trade
    public function show()
    {
        $userId = Auth::id();

        $trade = $this->tradeService->findPendingTrade($userId);

        if (!$trade) {
            return $this->res(404, [], 'No pending trade found');
        }

        return $this->res(200, $trade, '');
    }

    //accept trade
    public function accept($id)
    {
        $userId = Auth::id();

        $trade = $this->tradeService->findPendingTrade($userId);

        if (!$trade) {
            return $this->res(404, [], 'Trade not found or not allowed');
        }

        $this->tradeService->executeTrade($trade);

        return $this->res(200, $trade, 'Trade accepted successfully');
    }

    //reject trade
    public function reject($id)
    {
        $userId = Auth::id();

        $trade = $this->tradeService->findPendingTrade($userId);

        if (!$trade) {
            return $this->res(404, [], 'Trade not found or not allowed to reject');
        }

        $this->tradeService->rejectTrade($trade);

        return $this->res(200, $trade, 'Trade rejected successfully');
    }
}
