<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class LoginController extends BaseController
{
    //登入功能
    public function login(Request $request)
    {
        //檢查Cache，確任是否已登入
        if (Cache::has('user_data')) {
            return $this->logintest();
        }
        //確認輸入帳密是否正確
        $email = $request->email;
        $password = $request->password;
        $user = User::where("email", $email)->where("password", $password)->get();
        if (!empty($user[0])) {
            $user = $user[0];
            Cache::put("user_data", [
                "name" => $user->name,
                "email" => $user->email
            ]);
            return $this->same([$user], "登錄成功", 200);
        } else {
            return $this->same([$user], "登錄失敗", 404);
        }
    }

    //確認是否登錄成功的功能
    public function logintest()
    {
        $user_data = Cache::get('user_data');
        if (Cache::has('user_data')) {
            $user_data = Cache::get('user_data');
            return $this->same([$user_data], "已登入", 200);;
        } else {
            return $this->same([$user_data], "尚未登入", 200);
        }
    }

    //登出功能
    public function logout()
    {
        if (Cache::has('user_data')) {
            Cache::forget('user_data');
            return "已登出";
        } else {
            return "尚未登入";
        }
    }
}
