<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\RegisterRequest;
use App\Models\User;

class RegisterController extends BaseController
{
    public function store(RegisterRequest $request)
    {
        //輸入用戶註冊資料
        $user = new User();
        $user->name = $request->input("name");
        $user->email = $request->input("email");
        //將密碼進行加密
        $user->password = bcrypt($request->input("password"));
        $user->save();
        //另一個輸入註冊資料的方法(密碼無加密)
        //$user=User::create($request->toArray());

        //存入cache
        Cache::put("user_data", [
            "name" => $user->name,
            "email" => $user->email
        ]);
        return $this->same([$user], "註冊成功", 201);
    }
}
