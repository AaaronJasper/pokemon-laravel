<?php

namespace App\Http\Controllers;

use App\Events\UserRegister;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends BaseController
{
    public function __construct()
    {
        $this->middleware("auth:sanctum")->only("googleLogout");
    }
    //進入google的頁面
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
            $token = $findUser->createToken("myapptoken")->plainTextToken;
            return $this->res(200, $token, "Login success");
        }
        //如果會員資料庫中沒有 Google 帳戶資料，將檢查資料庫中有無會員 email，如果有僅加入 Google 帳戶資料後導向主控台
        if ($existUser != '' && $existUser->email === $user->email) {
            $existUser->google_account = $user->id;
            $existUser->save();
            $token = $existUser->createToken("myapptoken")->plainTextToken;
            return $this->res(200, $token, "Login success");
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
            return $this->res(200, $token, "Login success");
        }
    }
}

