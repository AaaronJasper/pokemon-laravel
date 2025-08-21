<?php

namespace App\Http\Controllers;

use App\Http\Requests\LikePokemonRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pokemon;
use App\Models\User;

class LikeController extends BaseController
{
    /**
     * Like a Pokemon
     *
     * Mark a Pokemon as liked by the authenticated user.
     *
     * @authenticated
     * @header Authorization Bearer {token} required The access token of the authenticated user. Example: Bearer 196|wQr6eQ7dvE2cjGyztjIWJeGqCWa0GhVSON2Z7EcC
     * @bodyParam pokemon_id int required The ID of the Pokemon. Example: 132
     *
     * @response{
     *   "code": 201,
     *   "data": [],
     *   "message": "Liked successfully"
     * }
     */
    public function like(LikePokemonRequest $request)
    {
        $user = Auth::user();

        $pokemon = Pokemon::findOrFail($request->pokemon_id);

        // 避免重複
        if (!$user->likedPokemons()->where('pokemon_id', $pokemon->id)->exists()) {
            $user->likedPokemons()->attach($pokemon->id);
        }

        return $this->res(201, [], "Liked successfully");
    }

    /**
     * Unlike a Pokemon
     *
     * Remove a like from a Pokemon for the authenticated user.
     *
     * @authenticated
     * @header Authorization Bearer {token} required The access token of the authenticated user. Example: Bearer 196|wQr6eQ7dvE2cjGyztjIWJeGqCWa0GhVSON2Z7EcC
     * @bodyParam pokemon_id int required The ID of the Pokemon to unlike. Example: 132
     *
     * @response 200 {
     *   "code": 200,
     *   "data": [],
     *   "message": "Unliked successfully"
     * }
     */
    public function unlike(LikePokemonRequest $request)
    {
        $user = Auth::user();
        $pokemon = Pokemon::findOrFail($request->pokemon_id);
        $user->likedPokemons()->detach($pokemon->id);

        return $this->res(200, [], "Unliked successfully");
    }

    /**
     * Get top liked Pokémon
     *
     * Retrieve the top 7 Pokémon that have been liked by users.
     *
     * @response 200 {
     *   "code": 200,
     *   "data": [
     *     {
     *       "id": 132,
     *       "name": "wugtrio",
     *       "level": 99,
     *       "race": "wugtrio",
     *       "nature_id": 26,
     *       "ability_id": 267,
     *       "status": 1,
     *       "created_at": "2025-06-11T06:32:48.000000Z",
     *       "updated_at": "2025-08-18T08:23:44.000000Z",
     *       "skill1": "slam",
     *       "skill2": "sand-attack",
     *       "skill3": "headbutt",
     *       "skill4": "wrap",
     *       "user_id": 1,
     *       "image_url": "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/961.png",
     *       "is_trading": 0,
     *       "liked_by_users_count": 3,
     *       "user": {
     *         "id": 1,
     *         "name": "default",
     *         "email": "default@default.com",
     *         "email_verified_at": null,
     *         "two_factor_confirmed_at": null,
     *         "current_team_id": null,
     *         "profile_photo_path": null,
     *         "created_at": "2025-05-26T07:51:56.000000Z",
     *         "updated_at": "2025-05-26T07:51:56.000000Z",
     *         "google_account": null
     *       }
     *     },
     *     {
     *       "id": 131,
     *       "name": "rayquaza",
     *       "level": 6,
     *       "race": "rayquaza",
     *       "nature_id": 26,
     *       "ability_id": 267,
     *       "status": 1,
     *       "created_at": "2025-06-10T05:31:46.000000Z",
     *       "updated_at": "2025-07-26T05:19:08.000000Z",
     *       "skill1": null,
     *       "skill2": null,
     *       "skill3": null,
     *       "skill4": null,
     *       "user_id": 4,
     *       "image_url": "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/384.png",
     *       "is_trading": 0,
     *       "liked_by_users_count": 3,
     *       "user": {
     *         "id": 4,
     *         "name": "aaaa",
     *         "email": "a@a.com",
     *         "email_verified_at": null,
     *         "two_factor_confirmed_at": null,
     *         "current_team_id": null,
     *         "profile_photo_path": null,
     *         "created_at": "2025-06-04T06:00:41.000000Z",
     *         "updated_at": "2025-06-04T06:00:41.000000Z",
     *         "google_account": null
     *       }
     *     }
     *   ],
     *   "message": "Top liked Pokémon fetched successfully"
     * }
     */
    public function topLikedPokemons()
    {
        $topPokemons = Pokemon::with(['user'])
            ->withCount('likedByUsers')
            ->having('liked_by_users_count', '>', 0)
            ->where('status', true)
            ->orderByDesc('liked_by_users_count')
            ->take(7)
            ->get();

        return $this->res(200, $topPokemons, 'Top liked Pokémon fetched successfully');
    }
}
