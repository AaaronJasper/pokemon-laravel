<?php

use App\Http\Controllers\AbilityController;
use App\Http\Controllers\NatureController;
use App\Http\Controllers\PokemonController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
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

Route::resource('pokemon', PokemonController::class)->except("edit", "create");
//用戶註冊
Route::post("user/register", [UserController::class, 'register']);
//用戶登錄
Route::post("user/login", [UserController::class, 'login']);
//驗證信箱
Route::get("/verify/{token}", [UserController::class, "verify"])->name("verify");
//寄送忘記密碼郵件
Route::post('/forget_password', [UserController::class, "forget_password"]);
//重設密碼
Route::post("/reset_password/{token}", [UserController::class, "reset_password"])->name("reset_password");
//需登入路由
Route::middleware('auth:sanctum')->group(function () {
    //用戶登出
    Route::delete("user/logout", [UserController::class, 'logout']);
    //再次發送驗證
    Route::post('/send_verify', [UserController::class, "send_verify"]);
    //性格
    Route::resource("nature", NatureController::class)->only('update', 'store');
    //特性
    Route::resource("ability", AbilityController::class)->only('update', 'store');
    //技能
    Route::get("pokemon/{id}/enableSkill", [SkillController::class, 'index']);
    Route::get("pokemon/{id}/skill", [SkillController::class, 'show']);
    Route::post("pokemon/{id}/skill", [SkillController::class, 'learn']);
});
//google登入(需用web中間件才能讓套件產生作用)
//也可以直接加在web.php
Route::group(['middleware' => ['web']], function () {
    Route::get('/auth/google', [\App\Http\Controllers\SocialiteController::class, 'googleLogin'])->name('/auth/google');
    Route::get('/auth/google/callback', [\App\Http\Controllers\SocialiteController::class, 'googleLoginCallback'])->name('/auth/google/callback');
    Route::get('/auth/google/logout', [\App\Http\Controllers\SocialiteController::class, 'googleLogout'])->name('/auth/google/logout');
});

//測試
Route::get('test', [TestController::class, 'test'])->middleware("auth:sanctum");
