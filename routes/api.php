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
Route::middleware('api')->resource('pokemon', PokemonController::class)->except("edit", "create");
//需登入路由
Route::middleware('auth:sanctum')->group(function () {
    //性格
    Route::resource("nature", NatureController::class)->only('update', 'store');
    //特性
    Route::resource("ability", AbilityController::class)->only('update', 'store');
    //技能
    Route::get("pokemon/{id}/enableSkill", [SkillController::class, 'index']);
    Route::get("pokemon/{id}/skill", [SkillController::class, 'show']);
    Route::post("pokemon/{id}/skill", [SkillController::class, 'learn']);
    //交易
    Route::post("trade", [TradeController::class, 'store']);
    Route::get("trade", [TradeController::class, 'show']);
    Route::put("trade/{id}/accept", [TradeController::class, 'accept']);
    Route::put("trade/{id}/reject", [TradeController::class, 'reject']);
    Route::get("trade/history", [TradeController::class, 'history']);
    Route::get('trade/unread-notifications', [TradeController::class, 'showUnreadNotifications']);
    Route::post('trade/{trade}/mark-as-read', [TradeController::class, 'markAsRead']);

    //愛心功能
    Route::post("like", [LikeController::class, 'like']);
    Route::post("unlike", [LikeController::class, 'unlike']);
});
Route::get('/ranking/top-liked', [LikeController::class, 'topLikedPokemons']);
//AI 生成寶可夢描述
Route::post('/pokemon/describe', [PokemonController::class, 'generateDescription']);
//用戶註冊
Route::post("user/register", [UserController::class, 'register']);
//用戶登錄
Route::post("user/login", [UserController::class, 'login']);
//拿到 OAuth 登入 user資料
Route::post('oauth/exchange-token', [UserController::class, "exchange_token"]);
//需登入路由
Route::middleware('auth:sanctum')->group(function () {
    //用戶登出
    Route::delete("user/logout", [UserController::class, 'logout']);
    //再次發送驗證
    Route::post('/send_verify', [UserController::class, "send_verify"]);
});
//驗證信箱
Route::get("/verify/{token}", [UserController::class, "verify"])->name("verify");
//寄送忘記密碼郵件
Route::post('/forget_password', [UserController::class, "forget_password"]);
//重設密碼
Route::post("/reset_password/{token}", [UserController::class, "reset_password"])->name("reset_password");
//google登入(需用web中間件才能讓套件產生作用)
//也可以直接加在web.php
Route::group(['middleware' => ['web']], function () {
    Route::get('/auth/google', [\App\Http\Controllers\SocialiteController::class, 'googleLogin'])->name('/auth/google');
    Route::get('/auth/google/callback', [\App\Http\Controllers\SocialiteController::class, 'googleLoginCallback'])->name('/auth/google/callback');
});
//回傳未登入(讓middleware轉址)
Route::get('notLogin', [UserController::class, 'notLogin'])->name("notLogin");

//測試
//Route::get('test', [TestController::class, 'test'])->name("test");