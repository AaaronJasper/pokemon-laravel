<?php

namespace App\Http\Controllers;

use App\Events\UserRegister;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends BaseController
{
    //註冊
    public function register(RegisterRequest $request)
    {
        //創建用戶
        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => password_hash($request->input("password"), PASSWORD_DEFAULT),
            "created_at" => Carbon::now(),
        ]);
        //發送驗證信
        event(new UserRegister($request->input("email")));
        //回傳資料
        $data = new UserResource($user);
        return $this->res(201, $data, "Register success");
    }
    //登入
    public function login(LoginRequest $request)
    {
        //找用戶郵箱
        $user = User::where("email", $request->email)->first();
        //確認是否是黑單
        //if($user->is_locked==1){
        //$data = new UserResource($user);
        //return $this->re_response($data, "Login failed", 401);
        //}
        //確認密碼
        if (password_verify($request->password, $user->password)) {
            //建立登入token
            $token = $user->createToken("myapptoken")->plainTextToken;
            return $this->res(200, $token, "Login success");
        } else {
            $data = new UserResource($user);
            return $this->res(401, $data, "Login failed");
        }
    }
    //登出
    public function logout()
    {
        $userId = Auth::id();
        // 根據 ID 取得使用者
        $user = User::find($userId);
        $data = new UserResource($user);
        // 刪除特定的 token
        $user->tokens()->delete();
        return $this->res(200, $data, "Logout success");
    }
    //寄送驗證信
    public function send_verify(){
        $user=User::find(Auth::id());
        $data = new UserResource($user); 
        if($user->email_verified_at!=null){
            return $this->res(200, $data, "Already verify email");
        }
        event(new UserRegister($user->email));
        return $this->res(200, $data, "Email send successfully");
    }
    //執行驗證
    public function verify($token)
    {
        $registerUser = DB::table("register_token")->where([
            "token" => $token,
        ])->first();
        if (!$registerUser) {
            return $this->re_response([], "Already verify", 200);
        }
        //用戶加上驗證時間
        $select_user = User::where("email", $registerUser->email)->first();
        $select_user->email_verified_at = Carbon::now();
        $select_user->save();
        //刪除token
        DB::table("register_token")->where("token", $token)->delete();
        
        return $this->res(200, [], "Verify success");
    }
}
