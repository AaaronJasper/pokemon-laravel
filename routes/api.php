<?php

use App\Http\Controllers\AbilityController;
use App\Http\Controllers\NatureController;
use App\Http\Controllers\PokemonController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TradeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//});

// Resource routes for Pokemon, using API middleware
// exclude "edit" and "create" which are for web forms
Route::middleware('api')->resource('pokemon', PokemonController::class)->except("edit", "create");

// Routes that require authentication
Route::middleware('auth:sanctum')->group(function () {
    // Nature (性格)
    // Nature management routes (update, store)
    Route::resource("nature", NatureController::class)->only('update', 'store');

    // Ability (特性)
    // Ability management routes (update, store)
    Route::resource("ability", AbilityController::class)->only('update', 'store');

    // Skills (技能)
    // Get all enabled skills for a Pokemon
    Route::get("pokemon/{id}/enableSkill", [SkillController::class, 'index']);
    // Get learned skills of a Pokemon
    Route::get("pokemon/{id}/skill", [SkillController::class, 'show']);
    // Teach a skill to a Pokemon
    Route::post("pokemon/{id}/skill", [SkillController::class, 'learn']);

    // Trades (交易)
    // Create a trade
    Route::post("trade", [TradeController::class, 'store']);
    // Show trades
    Route::get("trade", [TradeController::class, 'show']);
    // Accept a trade
    Route::put("trade/{id}/accept", [TradeController::class, 'accept']);
    // Reject a trade
    Route::put("trade/{id}/reject", [TradeController::class, 'reject']);
    // Show trade history
    Route::get("trade/history", [TradeController::class, 'history']);
    // Show unread trade notifications
    Route::get('trade/unread-notifications', [TradeController::class, 'showUnreadNotifications']);
    // Mark a trade notification as read
    Route::post('trade/{trade}/mark-as-read', [TradeController::class, 'markAsRead']);

    // Like functionality (愛心功能)
    // Like a Pokemon
    Route::post("like", [LikeController::class, 'like']);
    // Unlike a Pokemon
    Route::post("unlike", [LikeController::class, 'unlike']);
});

// Top liked Pokemons ranking
// Return top liked Pokemons
Route::get('/ranking/top-liked', [LikeController::class, 'topLikedPokemons']);

// AI-generated Pokemon description
// Generate a Pokemon description using AI
Route::post('/pokemon/describe', [PokemonController::class, 'generateDescription']);

// User registration
// Register a new user
Route::post("user/register", [UserController::class, 'register']);

// User login
Route::post("user/login", [UserController::class, 'login']);

// Get OAuth login user information
// Exchange OAuth token to get user info
Route::post('oauth/exchange-token', [UserController::class, "exchange_token"]);

// Routes that require authentication
Route::middleware('auth:sanctum')->group(function () {
    // User logout
    Route::delete("user/logout", [UserController::class, 'logout']);
    // Resend verification email
    Route::post('/send_verify', [UserController::class, "send_verify"]);
});

// Email verification
Route::get("/verify/{token}", [UserController::class, "verify"])->name("verify");

// Send forgot password email
Route::post('/forget_password', [UserController::class, "forget_password"]);

// Reset user password using token
Route::post("/reset_password/{token}", [UserController::class, "reset_password"])->name("reset_password");

//google登入(需用web中間件才能讓套件產生作用)
//也可以直接加在web.php
// Google login (requires 'web' middleware to work properly)
// OAuth Google login routes
Route::group(['middleware' => ['web']], function () {
    Route::get('/auth/google', [\App\Http\Controllers\SocialiteController::class, 'googleLogin'])->name('/auth/google');
    Route::get('/auth/google/callback', [\App\Http\Controllers\SocialiteController::class, 'googleLoginCallback'])->name('/auth/google/callback');
});

//回傳未登入(讓middleware轉址)
// Return not logged-in response (for middleware redirection)
// Return notLogin response if user is not authenticated
Route::get('notLogin', [UserController::class, 'notLogin'])->name("notLogin");

// Testing route
// English: Test route (commented out)
//Route::get('test', [TestController::class, 'test'])->name("test");