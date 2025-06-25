<?php

namespace App\Http\Controllers;

use App\Http\Requests\LikePokemonRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pokemon;
use App\Models\User;

class LikeController extends BaseController
{
    public function like(LikePokemonRequest $request)
    {
        $user = Auth::user();

        $pokemon = Pokemon::findOrFail($request->pokemon_id);

        // 避免重複
        if (!$user->likedPokemons()->where('pokemon_id', $pokemon->id)->exists()) {
            $user->likedPokemons()->attach($pokemon->id);
        }

        return $this->res(201, [], "Created successfully");
    }

    public function unlike(LikePokemonRequest $request)
    {
        $user = Auth::user();
        $pokemon = Pokemon::findOrFail($request->pokemon_id);
        $user->likedPokemons()->detach($pokemon->id);

        return $this->res(200, [], "Unliked successfully");
    }

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
