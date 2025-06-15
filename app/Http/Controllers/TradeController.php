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

    /**
     * Initiate a trade
     * @response{
     * "code": 201,
     * "data": {
     *     "sender_id": 4,
     *     "receiver_id": 1,
     *     "sender_pokemon_id": 2,
     *     "receiver_pokemon_id": 112,
     *     "status": "pending",
     *     "updated_at": "2025-06-13T06:19:51.000000Z",
     *     "created_at": "2025-06-13T06:19:51.000000Z",
     *     "id": 16
     * },
     * "message": "Initiate trade successfully"
     * }
     */
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

    /**
     * Show a trade
     * @response{
     *   "code": 200,
     *   "data": {
     *       "id": 24,
     *       "sender_id": 5,
     *       "receiver_id": 4,
     *       "sender_pokemon_id": 127,
     *       "receiver_pokemon_id": 122,
     *       "status": "pending",
     *       "created_at": "2025-06-14T06:23:54.000000Z",
     *       "updated_at": "2025-06-14T06:23:54.000000Z"
     *   },
     *   "message": ""
     * }
     */
    public function show()
    {
        $userId = Auth::id();

        $trade = $this->tradeService->findPendingTrade($userId);

        if (!$trade) {
            return $this->res(404, [], 'No pending trade found');
        }

        return $this->res(200, $trade, '');
    }

    /**
     * Accept a trade
     * @response{
     *   "code": 200,
     *   "data": {
     *       "id": 24,
     *       "sender_id": 5,
     *       "receiver_id": 4,
     *       "sender_pokemon_id": 127,
     *       "receiver_pokemon_id": 122,
     *       "status": "accepted",
     *       "created_at": "2025-06-14T06:23:54.000000Z",
     *       "updated_at": "2025-06-14T06:25:46.000000Z",
     *   },
     *   "message": "Trade accepted successfully"
     * }
     */
    public function accept($id)
    {
        $userId = Auth::id();

        $trade = $this->tradeService->findPendingTradeByReceiver($userId);

        if (!$trade) {
            return $this->res(404, [], 'Trade not found or not allowed');
        }

        $this->tradeService->executeTrade($trade);

        return $this->res(200, $trade, 'Trade completed successfully');
    }

    /**
     * Reject a trade
     * @response{
     *   "code": 200,
     *   "data": {
     *       "id": 4,
     *       "sender_id": 1,
     *       "receiver_id": 4,
     *       "sender_pokemon_id": 2,
     *       "receiver_pokemon_id": 112,
     *       "status": "rejected",
     *       "created_at": "2025-06-10T06:55:25.000000Z",
     *       "updated_at": "2025-06-10T06:56:43.000000Z"
     *   },
     *   "message": "Trade rejected successfully"
     * }
     */
    public function reject()
    {
        $userId = Auth::id();

        $trade = $this->tradeService->findPendingTrade($userId);

        if (!$trade) {
            return $this->res(404, [], 'Trade not found or not allowed to reject');
        }

        $this->tradeService->rejectTrade($trade);

        return $this->res(200, $trade, 'Trade rejected successfully');
    }

    /**
     * Show all accepted trades
     *
     * @response {
     *     "code": 200,
     *     "data": [
     *         {
     *             "id": 8,
     *             "sender_id": 1,
     *             "receiver_id": 4,
     *             "sender_pokemon_id": 98,
     *             "receiver_pokemon_id": 124,
     *             "status": "accepted",
     *             "created_at": "2025-06-12T11:01:37.000000Z",
     *             "updated_at": "2025-06-12T11:05:12.000000Z"
     *         }
     *     ],
     *     "message": "Successfully retrieved 1 accepted trades."
     * }
     */
    public function history(){
        $userId = Auth::id();

        $trade = $this->tradeService->findAccomplishedTrade($userId);

        return $this->res(200, $trade, "Successfully retrieved " . count($trade) . " accepted trades.");
    }
}
