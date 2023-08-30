<?php

use App\Http\Controllers\AbilityController;
use App\Http\Controllers\NatureController;
use App\Http\Controllers\PokemonController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\TestController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
});

Route::resource('pokemon', PokemonController::class)->except("edit", "create");
Route::resource("nature", NatureController::class)->only('update', 'store');
Route::resource("ability", AbilityController::class)->only('update', 'store');
//技能
Route::get("pokemon/{id}/enableSkill", [SkillController::class, 'index']);
Route::get("pokemon/{id}/skill", [SkillController::class, 'show']);
Route::post("pokemon/{id}/skill", [SkillController::class, 'learn']);
//測試
Route::post('test', [TestController::class, 'test']);
