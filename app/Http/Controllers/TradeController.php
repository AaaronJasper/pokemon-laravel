<?php

namespace App\Http\Controllers;


use App\Http\Requests\StoreTradeRequest;
use App\Models\Pokemon;
use App\Models\Trade;
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
     * 
     * Allows an authenticated user to propose a trade between two Pokémon.
     * The trade status will initially be "pending" until accepted or rejected by the receiver.
     * 
     * @authenticated
     * @header Authorization Bearer {token} Example: Bearer 196|wQr6eQ7dvE2cjGyztjIWJeGqCWa0GhVSON2Z7EcC
     * @bodyParam sender_pokemon_id int required Example: 2
     * @bodyParam receiver_pokemon_id int required Example: 112
     * 
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
            $this->tradeService->sendTradeNotificationToReceiver($trade);
            return $this->res(201, $newTrade, "Initiate trade successfully");
        }
        else{
            return $this->res(403, [], "Fail initiate trade ");
        }
    }

    /**
     * Show a trade
     * 
     * Retrieves the pending Pokémon trade for the currently authenticated user, if any. 
     * Includes sender, receiver, Pokémon involved, and trade status.
     * Requires authentication.
     * 
     * @authenticated
     * @header Authorization Bearer {token} Example: Bearer 196|wQr6eQ7dvE2cjGyztjIWJeGqCWa0GhVSON2Z7EcC
     *
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
            return $this->res(404, [], 'No trade notification found');
        }

        return $this->res(200, $trade, '');
    }

    /**
     * Accept a trade
     * 
     * Accepts a pending Pokémon trade by the authenticated user. 
     * Updates the trade status to "accepted" and finalizes the exchange of Pokémon.
     * 
     * @authenticated
     * @header Authorization Bearer {token} Example: Bearer 196|wQr6eQ7dvE2cjGyztjIWJeGqCWa0GhVSON2Z7EcC
     * @urlParam id int required The ID of the Trade. Example: 24
     * 
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

        $this->tradeService->sendTradeNotificationToSender($trade, "completed");

        return $this->res(200, $trade, 'Trade completed successfully');
    }

    /**
     * Reject a trade
     * 
     * Rejects a pending Pokémon trade by the authenticated user. 
     * Updates the trade status to "rejected" and notifies the sender.
     * 
     * @authenticated
     * @header Authorization Bearer {token} Example: Bearer 196|wQr6eQ7dvE2cjGyztjIWJeGqCWa0GhVSON2Z7EcC
     * @urlParam id int required The ID of the Trade. Example: 24
     * 
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

        $this->tradeService->sendTradeNotificationToSender($trade, "rejected");

        return $this->res(200, $trade, 'Trade rejected successfully');
    }

    /**
     * Show all accepted trades
     * 
     * Retrieves a list of all accepted Pokémon trades involving the authenticated user, 
     * including details of the sender and receiver Pokémon.
     * 
     * @authenticated
     * @header Authorization Bearer {token} Example: Bearer 196|wQr6eQ7dvE2cjGyztjIWJeGqCWa0GhVSON2Z7EcC
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
     *             "updated_at": "2025-06-12T11:05:12.000000Z",
     *             "sender_pokemon": {
     *                 "id": 98,
     *                 "name": "Gengar no.98",
     *                 "level": 50,
     *                 "race": "gengar",
     *                 "nature_id": 1,
     *                 "ability_id": 1,
     *                 "status": 1,
     *                 "created_at": "2025-05-26T07:51:56.000000Z",
     *                 "updated_at": "2025-06-12T11:05:12.000000Z",
     *                 "skill1": "mega-punch",
     *                 "skill2": null,
     *                 "skill3": null,
     *                 "skill4": null,
     *                 "user_id": 4,
     *                 "image_url": "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/94.png",
     *                 "is_trading": 0
     *             },
     *             "receiver_pokemon": {
     *                 "id": 124,
     *                 "name": "garchomp",
     *                 "level": 77,
     *                 "race": "garchomp",
     *                 "nature_id": 26,
     *                 "ability_id": 267,
     *                 "status": 1,
     *                 "created_at": "2025-06-06T12:49:00.000000Z",
     *                 "updated_at": "2025-06-12T11:05:12.000000Z",
     *                 "skill1": null,
     *                 "skill2": null,
     *                 "skill3": null,
     *                 "skill4": null,
     *                 "user_id": 1,
     *                 "image_url": "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/445.png",
     *                 "is_trading": 0
     *             }
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
    
    /**
     * Response when a trade is accepted and the user is the sender
     *
     * Retrieves unread notifications related to trades for the authenticated user, 
     * including the user's role (sender or receiver) and trade details.
     * 
     * @authenticated
     * @header Authorization Bearer {token} Example: Bearer 196|wQr6eQ7dvE2cjGyztjIWJeGqCWa0GhVSON2Z7EcC
     *
     * @response {
     *   "code": 200, 
     *   "data": {
     *     "has_unread": true,             
     *     "role": "sender",               
     *     "trade_id": 152,               
     *     "trade": {
     *       "id": 152,                  
     *       "sender_id": 4,             
     *       "receiver_id": 5,           
     *       "sender_pokemon_id": 121,   
     *       "receiver_pokemon_id": 131,  
     *       "status": "accepted",        
     *       "created_at": "2025-07-26T05:07:14.000000Z", 
     *       "updated_at": "2025-07-26T05:07:33.000000Z", 
     *       "sender_is_read": 1,       
     *       "receiver_is_read": 0        
     *     },
     *     "status": "accepted"           
     *   },
     *   "message": ""                     
     * }
     */

    public function showUnreadNotifications(){
        $user = Auth::user();

        $trade = $this->tradeService->getUnreadNotifications($user->id);

        return $this->res(200, $trade, '');
    }

    /**
     * Response when a trade notification is successfully marked as read.
     * 
     * * Marks the trade notification as read for the authenticated user, depending on whether 
     * the user is the sender or receiver.
     * 
     * @authenticated
     * @header Authorization Bearer {token} Example: Bearer 196|wQr6eQ7dvE2cjGyztjIWJeGqCWa0GhVSON2Z7EcC
     *
     * 
     * @response{
     *  "code": 200, 
     *  "data": "",
     *  "message": "Marked as read",
     * }
     */
    public function markAsRead(Trade $trade){
        
        $user = Auth::user();

        if ($trade->sender_id === $user->id) {
            $trade->sender_is_read = false;
        }

        if ($trade->receiver_id === $user->id) {
            $trade->receiver_is_read = false;
        }

        $trade->save();

        return $this->res(200, "", "Marked as read");

    }
}
