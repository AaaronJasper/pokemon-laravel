<?php

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
    return $request->user();
});

//註冊路由
Route::post('register', [\App\Http\Controllers\RegisterController::class,'store']);
//登陸路由
Route::post('login', [\App\Http\Controllers\LoginController::class,'login']);
Route::get("logintest",[\App\Http\Controllers\LoginController::class,'logintest']);
Route::delete("logout",[\App\Http\Controllers\LoginController::class,'logout']);
//用戶列表(只用index和show方法)
Route::apiResource('users', \App\Http\Controllers\UserController::class,["only" => ["index", "show"]]);
//測試路由
Route::get("test",[\App\Http\Controllers\TestController::class,'index']);

