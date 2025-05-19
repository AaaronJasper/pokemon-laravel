<?php

namespace App\Http\Controllers;

use App\Events\UserRegister;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class SocialiteController extends BaseController
{
    public function __construct()
    {
        $this->middleware("auth:sanctum")->only("googleLogout");
    }
    /**
     *進入google頁面
     */
    public function googleLogin()
    {
        return Socialite::driver('google')->redirect();
    }
    /**
     *google登入用戶
     * @response{
     * "code": 200,
     * "data": "2|JgAcW87DjmPYIX2uyFhdATblRGWYnqODmEGLGe5q",
     * "message": "Login success"
     * }
     */
    public function googleLoginCallback()
    {
        $user = Socialite::driver('google')->stateless()->user();
        $existUser = User::where('email', $user->email)->first();
        $findUser = User::where('google_account', $user->id)->first();

        if ($findUser) {

            if (!$findUser->email_verified_at) {
                $findUser->email_verified_at = Carbon::now();
                $findUser->save();
            }

            $token = $findUser->createToken("myapptoken")->plainTextToken;
            $code = Str::uuid();
            Cache::put("oauth_code:{$code}", $findUser->id, now()->addMinutes(3));
            return redirect('http://localhost:5173/OAuthCallback/' . $code);       
        }
        //如果會員資料庫中沒有 Google 帳戶資料，將檢查資料庫中有無會員 email，如果有僅加入 Google 帳戶資料後導向主控台
        if ($existUser != '' && $existUser->email === $user->email) {
            $existUser->google_account = $user->id;

            if (!$existUser->email_verified_at) {
                $existUser->email_verified_at = Carbon::now();
            }
    
            $existUser->save();

            $token = $existUser->createToken("myapptoken")->plainTextToken;
            $code = Str::uuid();
            Cache::put("oauth_code:{$code}", $existUser->id, now()->addMinutes(3));
            return redirect('http://localhost:5173/OAuthCallback/' . $code);       
        } else {
            //資料庫無會員資料時註冊會員資料，然後導向主控台
            $newUser = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'google_account' => $user->id,
                'password' => encrypt('fromsocialwebsite'),
                'email_verified_at' => Carbon::now()
            ]);
            $token = $newUser->createToken("myapptoken")->plainTextToken;
            $code = Str::uuid();
            Cache::put("oauth_code:{$code}", $newUser->id, now()->addMinutes(3));
            return redirect('http://localhost:5173/OAuthCallback/' . $code);       
        }
    }
}

